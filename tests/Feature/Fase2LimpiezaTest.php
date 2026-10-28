<?php

use App\Enums\Category;
use App\Enums\ShipmentStatus;
use App\Enums\Style;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\File;

test('las vistas usan Vite y ya no cargan el CDN de Tailwind ni Bootstrap', function () {
    $vistas = collect(File::allFiles(resource_path('views')))
        ->map(fn ($fichero) => $fichero->getContents())
        ->implode("\n");

    expect($vistas)
        ->not->toContain('cdn.tailwindcss.com')
        ->not->toContain('class="card')
        ->not->toContain('form-control')
        ->not->toContain('<livewire:')
        ->not->toContain('filepath:');

    expect(File::get(resource_path('views/layouts/app.blade.php')))->toContain('@vite(');
});

test('Livewire, Volt y Breeze ya no están instalados', function () {
    $composer = json_decode(File::get(base_path('composer.json')), true);
    $paquetes = array_keys($composer['require'] + $composer['require-dev']);

    expect($paquetes)
        ->not->toContain('livewire/livewire')
        ->not->toContain('livewire/volt')
        ->not->toContain('laravel/breeze')
        ->not->toContain('doctrine/dbal')
        ->toContain('laravel/ui');
});

test('los botones de la galería salen del enum de estilos', function () {
    $respuesta = $this->get('/galeria')->assertOk();

    foreach (Style::cases() as $estilo) {
        $respuesta->assertSee('data-style="'.$estilo->value.'"', false)
            ->assertSee($estilo->background());
    }
});

test('los formularios ofrecen los estilos y categorías de los enums', function () {
    $respuesta = $this->actingAs(admin())->get('/galeria/nuevo')->assertOk();

    foreach ([...Style::cases(), ...Category::cases()] as $opcion) {
        $respuesta->assertSee('<option value="'.$opcion->value.'"', false)
            ->assertSee($opcion->label());
    }
});

test('un presupuesto con un estilo o categoría inventados se rechaza', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/presupuesto', [
        'description' => 'x', 'height' => 1, 'width' => 1,
        'style' => 'impresionismo', 'category' => 'fantasia',
    ])->assertSessionHasErrors(['style', 'category']);

    expect(Shipment::count())->toBe(0);
});

test('los presupuestos antiguos con valores que ya no existen se siguen viendo', function () {
    $user = User::factory()->create();
    $user->shipments()->create([
        'description' => 'Encargo antiguo', 'height' => 1, 'width' => 1,
        'style' => 'realismo', 'category' => 'Fantasia',
    ]);

    $this->actingAs(admin())->get('/admin/shipments')
        ->assertOk()
        ->assertSee('realismo')
        ->assertSee('Fantasia')
        ->assertSee('Pendiente');
});

test('el dashboard muestra el estado y el estilo en español', function () {
    $user = User::factory()->create();
    $user->shipments()->create([
        'description' => 'Mi encargo', 'height' => 1, 'width' => 1,
        'style' => Style::Oleo->value, 'category' => Category::Retrato->value,
        'status' => ShipmentStatus::Reviewed,
    ]);

    $this->actingAs($user)->get('/dashboard')
        ->assertOk()
        ->assertSee('Óleo')
        ->assertSee('Retrato')
        ->assertSee('Revisado');
});

test('las obras devuelven estilo y categoría como enums', function () {
    $obra = Product::create([
        'name' => 'Obra', 'description' => 'd', 'height' => 1, 'width' => 1,
        'style' => 'lienzo', 'category' => 'urbano', 'imagen' => 'a.jpg',
    ])->fresh();

    expect($obra->style)->toBe(Style::Lienzo);
    expect($obra->category)->toBe(Category::Urbano);

    // La API sigue mandando el texto, que es lo que espera el carrusel
    $this->getJson('/api/products?style=lienzo')->assertJsonPath('0.style', 'lienzo');
});

test('el middleware admin está registrado sin Kernel ni providers propios', function () {
    expect(File::exists(app_path('Http/kernel.php')))->toBeFalse();
    expect(require base_path('bootstrap/providers.php'))->toBe([AppServiceProvider::class]);

    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    $this->actingAs(admin())->get('/admin')->assertOk();
});
