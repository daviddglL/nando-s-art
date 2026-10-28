<?php

use App\Actions\SolicitarPresupuesto;
use App\Enums\EstadoEncargo;
use App\Models\Encargo;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

// --- API: el estado que recibe la galería (ProductResource) ---

test('la API de obras devuelve la forma de ProductResource', function () {
    $obra = Product::factory()->create([
        'style' => 'oleo', 'category' => 'retrato', 'imagen' => 'mi obra.webp',
    ]);

    $this->getJson('/api/products?style=oleo')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $obra->id)
        ->assertJsonPath('data.0.style', 'oleo')
        ->assertJsonPath('data.0.style_label', 'Óleo')
        ->assertJsonPath('data.0.category_label', 'Retrato')
        ->assertJsonPath('data.0.imagen_url', url('/storage/obras/mi%20obra.webp'))
        ->assertJsonMissingPath('data.0.created_at');
});

test('la API de obras rechaza un estilo que no existe', function () {
    $this->getJson('/api/products?style=impresionismo')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('style');
});

test('una obra suelta también sale con ProductResource', function () {
    $obra = Product::factory()->create();

    $this->getJson("/api/products/{$obra->id}")
        ->assertOk()
        ->assertJsonPath('data.name', $obra->name);
});

// --- Galería: el script ya no está en la vista ---

test('la galería carga los módulos MVI y su configuración', function () {
    $this->get('/galeria')
        ->assertOk()
        ->assertSee('id="galeria-config"', false)
        ->assertSee('aria-label="Obra siguiente"', false)
        ->assertDontSee('fetch(', false);
});

// --- Obras: FormRequest + Action + Policy ---

test('el admin crea una obra y la imagen se guarda con nombre aleatorio', function () {
    Storage::fake('public');

    $this->actingAs(admin())->post('/galeria', [
        'name' => 'Nueva', 'description' => 'd', 'height' => 20, 'width' => 30,
        'style' => 'acuarela', 'category' => 'paisaje',
        'imagen' => UploadedFile::fake()->image('original.jpg'),
    ])->assertRedirect('/galeria');

    $obra = Product::sole();
    expect($obra->imagen)->not->toBe('original.jpg');
    Storage::disk('public')->assertExists('obras/'.$obra->imagen);
});

test('ProductPolicy solo deja crear obras al admin', function () {
    expect(admin()->can('create', Product::class))->toBeTrue()
        ->and(User::factory()->create()->can('create', Product::class))->toBeFalse();
});

// --- Encargos: el antiguo Shipment ---

test('la tabla shipments se ha renombrado a encargos', function () {
    expect(Schema::hasTable('encargos'))->toBeTrue()
        ->and(Schema::hasTable('shipments'))->toBeFalse();
});

test('la dirección antigua de presupuestos redirige a la nueva', function () {
    $this->actingAs(admin())
        ->get('/admin/shipments')
        ->assertRedirect('/admin/encargos');
});

test('el encargo es del cliente aunque el formulario envíe otro user_id', function () {
    Storage::fake('public');
    $cliente = User::factory()->create();
    $otro = User::factory()->create();

    $this->actingAs($cliente)->post('/presupuesto', [
        'user_id' => $otro->id,
        'description' => 'Retrato familiar', 'height' => 30, 'width' => 40,
        'style' => 'acuarela', 'category' => 'retrato',
        'imagen' => UploadedFile::fake()->image('referencia.png'),
    ])->assertRedirect('/dashboard');

    $encargo = Encargo::sole();
    expect($encargo->user_id)->toBe($cliente->id)
        ->and($encargo->status)->toBe(EstadoEncargo::Pending);
    Storage::disk('public')->assertExists($encargo->imagen);
});

test('la acción SolicitarPresupuesto funciona sin imagen', function () {
    $cliente = User::factory()->create();

    $encargo = app(SolicitarPresupuesto::class)->handle($cliente, [
        'description' => 'Sin imagen', 'height' => 1, 'width' => 1,
        'style' => 'oleo', 'category' => 'otro',
    ]);

    expect($encargo->imagen)->toBeNull()
        ->and($cliente->encargos()->count())->toBe(1);
});

test('EncargoPolicy: cada cliente ve los suyos y solo el admin lista y borra', function () {
    $cliente = User::factory()->create();
    $encargo = Encargo::factory()->for($cliente)->create();
    $otro = User::factory()->create();
    $admin = admin();

    expect($cliente->can('view', $encargo))->toBeTrue()
        ->and($otro->can('view', $encargo))->toBeFalse()
        ->and($admin->can('view', $encargo))->toBeTrue()
        ->and($cliente->can('viewAny', Encargo::class))->toBeFalse()
        ->and($admin->can('viewAny', Encargo::class))->toBeTrue()
        ->and($cliente->can('delete', $encargo))->toBeFalse()
        ->and($admin->can('delete', $encargo))->toBeTrue();
});

test('un cliente no puede borrar encargos', function () {
    $encargo = Encargo::factory()->create();

    $this->actingAs($encargo->user)
        ->delete("/admin/encargos/{$encargo->id}")
        ->assertForbidden();

    expect(Encargo::count())->toBe(1);
});

// --- Mensajes de validación en español ---

test('los errores de validación salen en español', function () {
    $this->actingAs(User::factory()->create())
        ->post('/presupuesto', ['height' => 'mucho'])
        ->assertSessionHasErrors([
            'description' => 'El campo descripción es obligatorio.',
            'height' => 'El campo altura debe ser un número.',
            'style' => 'El campo estilo es obligatorio.',
        ]);
});

test('el login fallido avisa en español', function () {
    $this->post('/login', ['email' => 'nadie@example.com', 'password' => 'x'])
        ->assertSessionHasErrors(['email' => 'Estas credenciales no coinciden con nuestros registros.']);
});
