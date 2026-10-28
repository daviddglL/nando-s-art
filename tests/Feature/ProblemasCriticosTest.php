<?php

use App\Models\Encargo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

// --- Imágenes de referencia de los presupuestos: privadas ---

function encargoConImagen(User $cliente): Encargo
{
    test()->actingAs($cliente)->post('/presupuesto', [
        'description' => 'Retrato de mi perro', 'height' => 30, 'width' => 40,
        'style' => 'acuarela', 'category' => 'retrato',
        'imagen' => UploadedFile::fake()->image('perro.jpg'),
    ])->assertRedirect();

    return Encargo::sole();
}

test('la imagen del presupuesto se guarda en el disco privado, no en el público', function () {
    Storage::fake('local');
    Storage::fake('public');

    $encargo = encargoConImagen(User::factory()->create());

    Storage::disk('local')->assertExists($encargo->imagen);
    expect(Storage::disk('public')->allFiles())->toBeEmpty()
        ->and($encargo->imagenUrl())->toBe(route('encargos.imagen', $encargo))
        ->and($encargo->imagenUrl())->not->toContain('/storage/');
});

test('solo el cliente dueño y el admin pueden ver la imagen del presupuesto', function () {
    Storage::fake('local');
    $cliente = User::factory()->create();
    $encargo = encargoConImagen($cliente);
    $url = route('encargos.imagen', $encargo);

    auth()->logout();
    $this->get($url)->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();

    $this->actingAs($cliente)->get($url)
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Content-Type', 'image/jpeg');
    $this->actingAs(admin())->get($url)->assertOk();
});

test('un presupuesto sin imagen da 404 en la ruta de la imagen', function () {
    $cliente = User::factory()->create();
    $encargo = Encargo::factory()->for($cliente)->create(['imagen' => null]);

    $this->actingAs($cliente)->get(route('encargos.imagen', $encargo))->assertNotFound();
});

test('al borrar el presupuesto o la cuenta del cliente se borra su imagen', function () {
    Storage::fake('local');
    $cliente = User::factory()->create();
    $encargo = encargoConImagen($cliente);

    $this->actingAs(admin())->delete(route('encargos.destroy', $encargo))->assertRedirect();
    Storage::disk('local')->assertMissing($encargo->imagen);

    $otro = encargoConImagen($cliente);
    $cliente->delete();
    Storage::disk('local')->assertMissing($otro->imagen);
    expect(Encargo::count())->toBe(0);
});

test('la migración mueve las imágenes antiguas del disco público al privado', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('public')->put('presupuestos/vieja.jpg', 'contenido');
    Encargo::factory()->create(['imagen' => 'presupuestos/vieja.jpg']);

    $migracion = require database_path('migrations/2026_10_28_140000_mover_imagenes_de_encargos_a_disco_privado.php');
    $migracion->up();

    Storage::disk('public')->assertMissing('presupuestos/vieja.jpg');
    expect(Storage::disk('local')->get('presupuestos/vieja.jpg'))->toBe('contenido');
});

// --- API de usuarios: cambiar contraseña o email exige la contraseña actual ---

test('la API no cambia la contraseña de la propia cuenta sin la contraseña actual', function () {
    $user = User::factory()->create(['password' => Hash::make('actual-123')]);

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['password' => 'nueva-12345'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('current_password');

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['password' => 'nueva-12345', 'current_password' => 'mal'])
        ->assertJsonValidationErrors('current_password');

    expect(Hash::check('actual-123', $user->fresh()->password))->toBeTrue();

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['password' => 'nueva-12345', 'current_password' => 'actual-123'])
        ->assertOk();

    expect(Hash::check('nueva-12345', $user->fresh()->password))->toBeTrue();
});

test('la API no cambia el email de la propia cuenta sin la contraseña actual', function () {
    $user = User::factory()->create(['password' => Hash::make('actual-123'), 'email' => 'antes@example.com']);

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['email' => 'ladron@example.com'])
        ->assertJsonValidationErrors('current_password');
    expect($user->fresh()->email)->toBe('antes@example.com');

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['email' => 'despues@example.com', 'current_password' => 'actual-123'])
        ->assertOk();
    expect($user->fresh())
        ->email->toBe('despues@example.com')
        ->email_verified_at->toBeNull();
});

test('el admin puede cambiar la contraseña de otro usuario sin conocer la suya', function () {
    $user = User::factory()->create();

    $this->actingAs(admin())
        ->putJson("/api/users/{$user->id}", ['password' => 'restablecida-1'])
        ->assertOk();

    expect(Hash::check('restablecida-1', $user->fresh()->password))->toBeTrue();
});
