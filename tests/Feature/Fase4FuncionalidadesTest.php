<?php

use App\Enums\EstadoEncargo;
use App\Models\Encargo;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NuevoPresupuesto;
use App\Notifications\PresupuestoActualizado;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

$obraValida = fn (array $extra = []) => array_merge([
    'name' => 'Atardecer en Cádiz', 'description' => 'Acuarela sobre papel', 'height' => 30, 'width' => 40,
    'style' => 'acuarela', 'category' => 'paisaje', 'year' => 2025, 'price' => 150, 'available' => '1',
], $extra);

// --- Obras: ficha, slug y orden ---

test('cada obra recibe un slug único que no cambia al renombrarla', function () {
    $a = Product::factory()->create(['name' => 'Atardecer en Cádiz']);
    $b = Product::factory()->create(['name' => 'Atardecer en Cádiz']);

    expect($a->slug)->toBe('atardecer-en-cadiz')
        ->and($b->slug)->toBe('atardecer-en-cadiz-2');

    $a->update(['name' => 'Otro nombre']);
    expect($a->fresh()->slug)->toBe('atardecer-en-cadiz');
});

test('las obras nuevas van al final del orden', function () {
    $a = Product::factory()->create();
    $b = Product::factory()->create();

    expect($b->sort_order)->toBeGreaterThan($a->sort_order);
});

test('la ficha pública de una obra muestra sus datos, SEO y lightbox', function () {
    $obra = Product::factory()->create([
        'name' => 'Retrato de Lola', 'description' => 'Óleo con luz de tarde', 'style' => 'oleo',
        'year' => 2024, 'price' => null, 'imagen' => 'lola.webp',
    ]);
    $otra = Product::factory()->create(['style' => 'oleo', 'name' => 'Otra al óleo']);

    $this->get("/obras/{$obra->slug}")
        ->assertOk()
        ->assertSee('<title>Retrato de Lola · Nando&#039;s Art</title>', false)
        ->assertSee('<meta name="description" content="Óleo con luz de tarde">', false)
        ->assertSee('property="og:image" content="'.url('/storage/obras/lola.webp').'"', false)
        ->assertSee('Precio a consultar')
        ->assertSee('2024')
        ->assertSee('data-lightbox-abrir="visor-obra"', false)
        ->assertSee('Otra al óleo');
});

test('una obra borrada o inexistente da un 404 con el estilo de la web', function () {
    $obra = Product::factory()->create();
    $obra->delete();

    $this->get("/obras/{$obra->slug}")->assertNotFound()->assertSee('Esta página no existe');
    $this->get('/obras/no-existe')->assertNotFound();
});

test('el sitemap y el robots.txt listan las obras', function () {
    $obra = Product::factory()->create();

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(route('obras.show', $obra->slug), false);

    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: '.route('sitemap'), false);
});

test('la API devuelve la ficha, el enlace y respeta el orden del admin', function () {
    $segunda = Product::factory()->create(['style' => 'acuarela', 'sort_order' => 20]);
    $primera = Product::factory()->create(['style' => 'acuarela', 'sort_order' => 10, 'price' => 99.5]);

    $this->getJson('/api/products?style=acuarela')
        ->assertOk()
        ->assertJsonPath('data.0.id', $primera->id)
        ->assertJsonPath('data.0.url', route('obras.show', $primera->slug))
        ->assertJsonPath('data.0.precio_texto', '99,50 €')
        ->assertJsonPath('data.1.id', $segunda->id);
});

test('la API filtra también por categoría', function () {
    Product::factory()->create(['style' => 'oleo', 'category' => 'retrato']);
    Product::factory()->create(['style' => 'oleo', 'category' => 'paisaje']);

    $this->getJson('/api/products?style=oleo&category=retrato')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.category', 'retrato');
});

test('la caché de la API se renueva al crear, editar o borrar una obra', function () {
    $obra = Product::factory()->create(['style' => 'sketch', 'name' => 'Primera']);
    $this->getJson('/api/products?style=sketch')->assertJsonCount(1, 'data');

    Product::factory()->create(['style' => 'sketch']);
    $this->getJson('/api/products?style=sketch')->assertJsonCount(2, 'data');

    $obra->update(['name' => 'Renombrada']);
    $this->getJson('/api/products?style=sketch')->assertJsonFragment(['name' => 'Renombrada']);

    $obra->delete();
    $this->getJson('/api/products?style=sketch')->assertJsonCount(1, 'data');
});

// --- Obras: panel del admin ---

test('el admin crea una obra con año, precio y disponibilidad', function () use ($obraValida) {
    Storage::fake('public');

    $this->actingAs(admin())
        ->post('/galeria', $obraValida(['imagen' => UploadedFile::fake()->image('a.jpg')]))
        ->assertRedirect('/galeria');

    $obra = Product::sole();
    expect($obra->year)->toBe(2025)
        ->and($obra->price)->toBe('150.00')
        ->and($obra->available)->toBeTrue()
        ->and($obra->featured)->toBeFalse()
        ->and($obra->slug)->toBe('atardecer-en-cadiz');
});

test('el admin ve la lista de obras y un cliente no', function () {
    Product::factory()->create(['name' => 'Mi obra']);

    $this->actingAs(admin())->get('/admin/obras')->assertOk()->assertSee('Mi obra');
    $this->actingAs(User::factory()->create())->get('/admin/obras')->assertForbidden();
    $this->post('/logout');
    $this->get('/admin/obras')->assertRedirect('/login');
});

test('el admin edita una obra y al cambiar la imagen se borra la anterior', function () use ($obraValida) {
    Storage::fake('public');
    Storage::disk('public')->put('obras/vieja.jpg', 'x');
    $obra = Product::factory()->create(['imagen' => 'vieja.jpg']);

    $this->actingAs(admin())->get("/admin/obras/{$obra->id}/editar")->assertOk()->assertSee($obra->name);

    $this->actingAs(admin())
        ->put("/admin/obras/{$obra->id}", $obraValida([
            'name' => 'Nuevo nombre', 'available' => null, 'featured' => '1',
            'imagen' => UploadedFile::fake()->image('nueva.png'),
        ]))
        ->assertRedirect('/admin/obras');

    $obra->refresh();
    expect($obra->name)->toBe('Nuevo nombre')
        ->and($obra->available)->toBeFalse()
        ->and($obra->featured)->toBeTrue()
        ->and($obra->imagen)->not->toBe('vieja.jpg');
    Storage::disk('public')->assertMissing('obras/vieja.jpg');
    Storage::disk('public')->assertExists('obras/'.$obra->imagen);
});

test('editar sin imagen nueva conserva la que había', function () use ($obraValida) {
    $obra = Product::factory()->create(['imagen' => 'se-queda.jpg']);

    $this->actingAs(admin())->put("/admin/obras/{$obra->id}", $obraValida())->assertRedirect();

    expect($obra->fresh()->imagen)->toBe('se-queda.jpg');
});

test('un cliente no puede editar, borrar ni reordenar obras', function () use ($obraValida) {
    $obra = Product::factory()->create();
    $cliente = User::factory()->create();

    $this->actingAs($cliente)->put("/admin/obras/{$obra->id}", $obraValida())->assertForbidden();
    $this->actingAs($cliente)->delete("/admin/obras/{$obra->id}")->assertForbidden();
    $this->actingAs($cliente)->patch("/admin/obras/{$obra->id}/mover", ['direccion' => 'arriba'])->assertForbidden();

    expect($obra->fresh()->trashed())->toBeFalse();
});

test('borrar manda la obra a la papelera y se puede restaurar', function () {
    $obra = Product::factory()->create(['name' => 'Obra borrada']);

    $this->actingAs(admin())->delete("/admin/obras/{$obra->id}")->assertRedirect('/admin/obras');
    expect($obra->fresh()->trashed())->toBeTrue();
    $this->getJson('/api/products')->assertJsonCount(0, 'data');
    $this->actingAs(admin())->get('/admin/obras')->assertSee('Papelera')->assertSee('Obra borrada');

    $this->actingAs(admin())->patch("/admin/obras/{$obra->id}/restaurar")->assertRedirect('/admin/obras');
    expect($obra->fresh()->trashed())->toBeFalse();
});

test('el admin cambia el orden de las obras con las flechas', function () {
    [$a, $b, $c] = Product::factory()->count(3)->create();

    $this->actingAs(admin())->patch("/admin/obras/{$c->id}/mover", ['direccion' => 'arriba'])->assertRedirect();
    expect(Product::ordenadas()->pluck('id')->all())->toBe([$a->id, $c->id, $b->id]);

    // La primera no puede subir más
    $this->actingAs(admin())->patch("/admin/obras/{$a->id}/mover", ['direccion' => 'arriba']);
    expect(Product::ordenadas()->pluck('id')->first())->toBe($a->id);

    $this->actingAs(admin())->patch("/admin/obras/{$a->id}/mover", ['direccion' => 'abajo']);
    expect(Product::ordenadas()->pluck('id')->all())->toBe([$c->id, $a->id, $b->id]);

    $this->actingAs(admin())->patch("/admin/obras/{$a->id}/mover", ['direccion' => 'de lado'])->assertSessionHasErrors('direccion');
});

// --- Encargos: avisos por email y respuesta del admin ---

test('al pedir un presupuesto se avisa por email al artista', function () {
    Notification::fake();
    $admin = admin();
    $cliente = User::factory()->create(['name' => 'Ana']);

    $this->actingAs($cliente)->post('/presupuesto', [
        'description' => 'Retrato de mi perro', 'height' => 30, 'width' => 30, 'style' => 'oleo', 'category' => 'retrato',
    ])->assertRedirect('/dashboard');

    Notification::assertSentTo($admin, NuevoPresupuesto::class, function ($aviso) use ($admin) {
        $correo = $aviso->toMail($admin);

        return $correo->subject === 'Nuevo presupuesto de Ana'
            && $correo->actionUrl === route('encargos.show', $aviso->encargo);
    });
    Notification::assertNotSentTo($cliente, NuevoPresupuesto::class);
});

test('el admin responde un presupuesto y el cliente recibe un email', function () {
    Notification::fake();
    $encargo = Encargo::factory()->create();

    $this->actingAs(admin())->get("/presupuestos/{$encargo->id}")->assertOk()->assertSee('Guardar y avisar al cliente');

    $this->actingAs(admin())->patch("/admin/encargos/{$encargo->id}", [
        'status' => 'reviewed', 'respuesta' => 'Me encanta la idea. Lo tendría en tres semanas.', 'precio' => 220,
    ])->assertRedirect("/presupuestos/{$encargo->id}");

    $encargo->refresh();
    expect($encargo->status)->toBe(EstadoEncargo::Reviewed)
        ->and($encargo->precio)->toBe('220.00')
        ->and($encargo->respondido_at)->not->toBeNull();

    Notification::assertSentTo($encargo->user, PresupuestoActualizado::class, function ($aviso) use ($encargo) {
        $texto = implode("\n", $aviso->toMail($encargo->user)->introLines);

        return str_contains($texto, 'Revisado') && str_contains($texto, 'tres semanas') && str_contains($texto, '220,00 €');
    });
});

test('si el admin guarda sin cambiar nada no se manda otro email', function () {
    Notification::fake();
    $encargo = Encargo::factory()->create(['status' => EstadoEncargo::Pending]);

    $this->actingAs(admin())->patch("/admin/encargos/{$encargo->id}", ['status' => 'pending'])->assertRedirect();

    Notification::assertNothingSent();
});

test('un cliente no puede responder presupuestos ni cambiarles el estado', function () {
    $encargo = Encargo::factory()->create();

    $this->actingAs($encargo->user)
        ->patch("/admin/encargos/{$encargo->id}", ['status' => 'completed'])
        ->assertForbidden();

    expect($encargo->fresh()->status)->toBe(EstadoEncargo::Pending);
});

test('el admin filtra los presupuestos por estado', function () {
    Encargo::factory()->create(['description' => 'Uno pendiente']);
    Encargo::factory()->create(['description' => 'Uno terminado', 'status' => EstadoEncargo::Completed]);

    $this->actingAs(admin())->get('/admin/encargos?estado=completed')
        ->assertOk()
        ->assertSee('Uno terminado')
        ->assertDontSee('Uno pendiente');

    $this->actingAs(admin())->get('/admin/encargos?estado=inventado')->assertSessionHasErrors('estado');
});

// --- Panel del cliente ---

test('el cliente ve la respuesta a su presupuesto pero no los de otros', function () {
    $encargo = Encargo::factory()->create([
        'status' => EstadoEncargo::Reviewed, 'respuesta' => 'Te lo hago encantado', 'precio' => 90, 'respondido_at' => now(),
    ]);

    $this->actingAs($encargo->user)->get('/dashboard')
        ->assertOk()
        ->assertSee('Ver respuesta')
        ->assertSee(route('encargos.show', $encargo), false);

    $this->actingAs($encargo->user)->get("/presupuestos/{$encargo->id}")
        ->assertOk()
        ->assertSee('Te lo hago encantado')
        ->assertSee('90,00 €')
        ->assertDontSee('Guardar y avisar al cliente');

    $this->actingAs(User::factory()->create())->get("/presupuestos/{$encargo->id}")->assertForbidden();
});

test('la imagen de referencia del presupuesto se guarda y se ve en el detalle', function () {
    Storage::fake('local');
    Notification::fake();
    $cliente = User::factory()->create();

    $this->actingAs($cliente)->post('/presupuesto', [
        'description' => 'Con foto', 'height' => 10, 'width' => 10, 'style' => 'sketch', 'category' => 'otro',
        'imagen' => UploadedFile::fake()->image('referencia.jpg'),
    ]);

    $encargo = Encargo::sole();
    Storage::disk('local')->assertExists($encargo->imagen);
    $this->actingAs($cliente)->get("/presupuestos/{$encargo->id}")->assertSee($encargo->imagenUrl(), false);
});

// --- Panel del admin, menú y páginas de error ---

test('el panel del admin resume obras y presupuestos pendientes', function () {
    Product::factory()->count(2)->create();
    Encargo::factory()->count(3)->create();

    $this->actingAs(admin())->get('/admin')
        ->assertOk()
        ->assertSeeInOrder(['2', 'obras en la galería', '3', 'presupuestos sin responder']);
});

test('el menú tiene botón para móvil y los mensajes de éxito se ven en el layout', function () {
    $this->withSession(['success' => 'Todo guardado'])
        ->get('/galeria')
        ->assertSee('data-menu-boton', false)
        ->assertSee('aria-controls="menu-principal"', false)
        ->assertSee('Todo guardado');
});

test('las páginas de error usan el estilo de la web', function () {
    $this->get('/no-existe')->assertNotFound()->assertSee('Esta página no existe')->assertSee('Ir a la galería');
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden()->assertSee('No tienes acceso');
});
