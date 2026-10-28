<?php

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('un invitado o un usuario normal no pueden subir imágenes sueltas', function () {
    Storage::fake('public');
    $imagen = UploadedFile::fake()->image('foto.jpg');

    $this->postJson('/api/upload-image', ['image' => $imagen])->assertUnauthorized();
    $this->actingAs(User::factory()->create())
        ->postJson('/api/upload-image', ['image' => $imagen])
        ->assertForbidden();

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

test('el admin sube imágenes con nombre aleatorio y no se aceptan SVG', function () {
    Storage::fake('public');
    $admin = admin();

    $this->actingAs($admin)
        ->postJson('/api/upload-image', ['image' => UploadedFile::fake()->image('mi foto.jpg')])
        ->assertOk();

    $ficheros = Storage::disk('public')->files('imagenes');
    expect($ficheros)->toHaveCount(1)
        ->and(basename($ficheros[0]))->not->toBe('mi foto.jpg');

    $svg = UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
    $this->actingAs($admin)
        ->postJson('/api/upload-image', ['image' => $svg])
        ->assertUnprocessable();
});

test('la ruta /api/test ya no existe', function () {
    $this->getJson('/api/test')->assertNotFound();
});

test('dos obras con el mismo nombre de archivo no comparten imagen', function () {
    Storage::fake('public');
    $admin = admin();

    $datos = fn () => [
        'name' => 'Obra', 'description' => 'd', 'height' => 30, 'width' => 40,
        'style' => 'acuarela', 'category' => 'paisaje',
        'imagen' => UploadedFile::fake()->image('cuadro.jpg'),
    ];

    $this->actingAs($admin)->post('/galeria', $datos())->assertRedirect('/galeria');
    $this->actingAs($admin)->post('/galeria', $datos())->assertRedirect('/galeria');

    $imagenes = Product::pluck('imagen');
    expect($imagenes->unique())->toHaveCount(2)
        ->and($imagenes)->not->toContain('cuadro.jpg');
    foreach ($imagenes as $imagen) {
        Storage::disk('public')->assertExists("obras/{$imagen}");
    }
});

test('una obra con un estilo que no existe se rechaza', function () {
    Storage::fake('public');

    $this->actingAs(admin())->post('/galeria', [
        'name' => 'Obra', 'description' => 'd', 'height' => 30, 'width' => 40,
        'style' => 'cubismo', 'category' => 'paisaje',
        'imagen' => UploadedFile::fake()->image('cuadro.jpg'),
    ])->assertSessionHasErrors('style');

    expect(Product::count())->toBe(0);
});

test('el rol no se puede asignar en masa', function () {
    $user = User::create([
        'name' => 'x', 'email' => 'x@example.com', 'password' => 'secreto123', 'role' => 'admin',
    ]);

    expect($user->fresh()->role)->toBe(Role::User);
});

test('la frase del día se cachea hasta el día siguiente', function () {
    Cache::flush();
    Http::fake(['frasedeldia.azurewebsites.net/*' => Http::response(['phrase' => 'Hola', 'author' => 'Yo'])]);

    $this->getJson('/frase-proxy')->assertOk()->assertJson(['phrase' => 'Hola']);
    $this->getJson('/frase-proxy')->assertOk();
    Http::assertSentCount(1);
});

test('la frase del día responde 503 si el servicio externo cae', function () {
    Cache::flush();
    Http::fake(['frasedeldia.azurewebsites.net/*' => Http::response(null, 500)]);
    $this->getJson('/frase-proxy')->assertStatus(503);
});

test('el formulario de presupuesto limita los envíos seguidos', function () {
    $user = User::factory()->create();
    $datos = ['description' => 'd', 'height' => 1, 'width' => 1, 'style' => 'acuarela', 'category' => 'otro'];

    for ($i = 0; $i < 5; $i++) {
        $this->actingAs($user)->post('/presupuesto', $datos)->assertRedirect();
    }

    $this->actingAs($user)->post('/presupuesto', $datos)->assertTooManyRequests();
});

test('la galería se renderiza una sola vez dentro del layout', function () {
    $html = $this->get('/galeria')->assertOk()->getContent();

    expect(substr_count(strtolower($html), '<!doctype html>'))->toBe(1)
        ->and(substr_count($html, '<body'))->toBe(1)
        ->and($html)->not->toContain('card.innerHTML');
});
