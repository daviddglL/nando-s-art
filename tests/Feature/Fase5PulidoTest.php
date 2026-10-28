<?php

use App\Enums\Style;
use App\Models\Encargo;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

// Obra con su imagen y sus miniaturas en el disco public (falso)
$obraConImagen = function (array $extra = []): Product {
    $obra = Product::factory()->create($extra);
    Storage::disk('public')->put('obras/'.$obra->imagen, 'imagen');
    Storage::disk('public')->put($obra->carpetaMiniaturas().'/400.webp', 'miniatura');

    return $obra;
};

// --- API ---

test('la API tiene límite de peticiones', function () {
    for ($i = 0; $i < 60; $i++) {
        $this->getJson('/api/products')->assertOk();
    }

    $this->getJson('/api/products')->assertTooManyRequests();
});

test('la ruta /api/upload-image ya no existe', function () {
    $this->actingAs(admin())->postJson('/api/upload-image')->assertNotFound();
});

test('la API de usuarios devuelve la lista paginada y sin contraseñas', function () {
    User::factory()->count(25)->create();

    $this->actingAs(admin())->getJson('/api/users')
        ->assertOk()
        ->assertJsonCount(20, 'data')
        ->assertJsonPath('total', 26)
        ->assertJsonMissingPath('data.0.password');
});

// --- Papelera de obras ---

test('el admin elimina definitivamente una obra de la papelera junto con su imagen', function () use ($obraConImagen) {
    Storage::fake('public');
    $obra = $obraConImagen();
    $obra->delete();

    $this->actingAs(admin())
        ->get('/admin/obras')
        ->assertSee('Eliminar definitivamente');

    $this->actingAs(admin())
        ->delete("/admin/obras/{$obra->id}/definitivo")
        ->assertRedirect('/admin/obras');

    expect(Product::withTrashed()->count())->toBe(0);
    Storage::disk('public')->assertMissing('obras/'.$obra->imagen);
    Storage::disk('public')->assertMissing($obra->carpetaMiniaturas().'/400.webp');
});

test('solo se elimina definitivamente desde la papelera y solo el admin', function () use ($obraConImagen) {
    Storage::fake('public');
    $obra = $obraConImagen();

    // Sin pasar por la papelera, no
    $this->actingAs(admin())->delete("/admin/obras/{$obra->id}/definitivo")->assertNotFound();

    $obra->delete();
    $this->actingAs(User::factory()->create())->delete("/admin/obras/{$obra->id}/definitivo")->assertForbidden();

    expect(Product::withTrashed()->count())->toBe(1);
    Storage::disk('public')->assertExists('obras/'.$obra->imagen);
});

test('model:prune borra las obras con más de 30 días en la papelera y sus imágenes', function () use ($obraConImagen) {
    Storage::fake('public');
    $vieja = $obraConImagen();
    $reciente = $obraConImagen();
    $publicada = $obraConImagen();

    $this->travel(-31)->days();
    $vieja->delete();
    $this->travelBack();
    $reciente->delete();

    $this->artisan('model:prune', ['--model' => [Product::class]])->assertSuccessful();

    expect(Product::withTrashed()->pluck('id')->all())->toEqualCanonicalizing([$reciente->id, $publicada->id]);
    Storage::disk('public')->assertMissing('obras/'.$vieja->imagen);
    Storage::disk('public')->assertExists('obras/'.$reciente->imagen);
});

test('la purga de la papelera está programada cada día', function () {
    $tareas = collect(app(Schedule::class)->events())->map(fn ($tarea) => $tarea->command);

    expect($tareas->contains(fn ($comando) => str_contains((string) $comando, 'model:prune')))->toBeTrue();
});

// --- Galería ---

test('la API busca por nombre o descripción y combina los filtros', function () {
    Product::factory()->create(['name' => 'Gato al sol', 'style' => 'oleo', 'category' => 'retrato']);
    Product::factory()->create(['name' => 'Calle', 'description' => 'Un gato en la calle', 'style' => 'oleo', 'category' => 'urbano']);
    Product::factory()->create(['name' => 'Gato azul', 'style' => 'acuarela', 'category' => 'retrato']);
    Product::factory()->create(['name' => 'Mar', 'description' => 'Olas', 'style' => 'oleo', 'category' => 'retrato']);

    $nombres = fn (string $query) => collect($this->getJson("/api/products?{$query}")->assertOk()->json('data'))->pluck('name')->sort()->values()->all();

    expect($nombres('style=oleo&q=gato'))->toBe(['Calle', 'Gato al sol'])
        ->and($nombres('style=oleo&category=retrato&q=GATO'))->toBe(['Gato al sol'])
        ->and($nombres('q=gato'))->toBe(['Calle', 'Gato al sol', 'Gato azul']);

    $this->getJson('/api/products?q='.str_repeat('a', 101))->assertUnprocessable();
});

test('la galería lee los filtros de la URL e ignora los que no existen', function () {
    $this->get('/galeria?estilo=oleo&categoria=retrato&q=gato')
        ->assertOk()
        ->assertSee('"estiloInicial":"oleo"', false)
        ->assertSee('"categoriaInicial":"retrato"', false)
        ->assertSee('"busquedaInicial":"gato"', false)
        ->assertSee('<option value="retrato" selected>', false)
        ->assertSee('id="filtro-busqueda"', false);

    $this->get('/galeria?estilo=nada&categoria=nada')
        ->assertOk()
        ->assertSee('"estiloInicial":"acuarela"', false)
        ->assertSee('"categoriaInicial":""', false);
});

test('el botón del estilo activo destaca con un tono oscuro y texto blanco', function () {
    foreach (Style::cases() as $style) {
        expect($style->buttonClasses())->toContain('text-white')
            ->and($style->buttonClasses())->not->toContain($style->background());
    }
});

// --- Presupuestos ---

test('"Pedir una obra parecida" rellena el presupuesto con el estilo y el tema de la obra', function () {
    $obra = Product::factory()->create(['name' => 'Gato al sol', 'style' => 'oleo', 'category' => 'retrato']);

    $this->get("/obras/{$obra->slug}")->assertSee("/presupuesto?obra={$obra->slug}", false);

    $this->actingAs(User::factory()->create())
        ->get("/presupuesto?obra={$obra->slug}")
        ->assertOk()
        ->assertSee('<option value="oleo" selected>', false)
        ->assertSee('<option value="retrato" selected>', false)
        ->assertSee('Me gustaría una obra parecida a «Gato al sol».');

    // Con una obra que no existe sale el formulario vacío
    $this->actingAs(User::factory()->create())
        ->get('/presupuesto?obra=no-existe')
        ->assertOk()
        ->assertDontSee('selected>', false);
});

test('cada etiqueta del formulario de presupuesto está asociada a su campo', function () {
    $html = $this->actingAs(User::factory()->create())->get('/presupuesto')->assertOk()->getContent();

    foreach (['description', 'height', 'width', 'style', 'category', 'imagen'] as $campo) {
        expect($html)->toContain("for=\"encargo-{$campo}\"")
            ->and($html)->toContain("id=\"encargo-{$campo}\" name=\"{$campo}\"");
    }
});

test('la tabla de encargos ya no tiene deleted_at y borrar un presupuesto lo borra de verdad', function () {
    expect(Schema::hasColumn('encargos', 'deleted_at'))->toBeFalse();

    $encargo = Encargo::factory()->create();
    $this->actingAs(admin())->delete("/admin/encargos/{$encargo->id}")->assertRedirect('/admin/encargos');

    expect(Encargo::count())->toBe(0);
});

// --- Menú ---

test('el admin tiene el panel en el menú y un cliente no', function () {
    $this->actingAs(admin())->get('/galeria')->assertSee('href="'.route('admin').'"', false);
    $this->actingAs(User::factory()->create())->get('/galeria')->assertDontSee('href="'.route('admin').'"', false);
});
