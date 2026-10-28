<?php

use App\Actions\GenerarMiniaturas;
use App\Http\Requests\SubirImagenesObraRequest;
use App\Models\ObraImagen;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Obra cuya imagen principal existe de verdad en el disco public (falso)
function obraConImagen(): Product
{
    $obra = Product::factory()->create(['imagen' => 'principal.jpg']);
    Storage::disk('public')->put('obras/principal.jpg', UploadedFile::fake()->image('principal.jpg', 900, 600)->getContent());

    return $obra;
}

beforeEach(fn () => Storage::fake('public'));

// --- Subir ---

test('el admin sube varias imágenes a la vez y cada una tiene sus miniaturas', function () {
    $obra = obraConImagen();

    $this->actingAs(admin())
        ->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [
            UploadedFile::fake()->image('detalle.jpg', 1000, 800),
            UploadedFile::fake()->image('firma.png', 500, 300),
        ]])
        ->assertRedirect(route('admin.obras.edit', $obra).'#imagenes')
        ->assertSessionHas('success', '2 imágenes añadidas');

    $imagenes = $obra->imagenes()->get();
    expect($imagenes)->toHaveCount(2)
        ->and($imagenes->pluck('sort_order')->all())->toBe([1, 2])
        ->and($imagenes[0]->anchosMiniaturas())->toBe([400, 800, 1000])
        ->and($imagenes[1]->anchosMiniaturas())->toBe([400, 500])
        // La principal no cambia
        ->and($obra->fresh()->imagen)->toBe('principal.jpg');

    foreach ($imagenes as $imagen) {
        Storage::disk('public')->assertExists('obras/'.$imagen->imagen);
        expect($imagen->imagen)->not->toContain('detalle')->not->toContain('firma');
    }
});

test('no se puede pasar del máximo de imágenes por obra ni subir ficheros que no son imágenes', function () {
    $obra = obraConImagen();
    ObraImagen::factory()->count(SubirImagenesObraRequest::MAXIMO_POR_OBRA - 1)->for($obra, 'obra')->create();
    $admin = admin();

    $this->actingAs($admin)
        ->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [
            UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg'),
        ]])
        ->assertSessionHasErrorsIn('imagenes', 'imagenes');

    $this->actingAs($admin)
        ->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->create('virus.php', 10, 'text/x-php')]])
        ->assertSessionHasErrorsIn('imagenes', 'imagenes.0');

    expect($obra->imagenes()->count())->toBe(SubirImagenesObraRequest::MAXIMO_POR_OBRA - 1);

    $this->actingAs($admin)
        ->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('c.jpg')]])
        ->assertSessionHasNoErrors();
    expect($obra->imagenes()->count())->toBe(SubirImagenesObraRequest::MAXIMO_POR_OBRA);
});

test('solo el admin gestiona las imágenes', function () {
    $obra = obraConImagen();
    $imagen = ObraImagen::factory()->for($obra, 'obra')->create();
    $cliente = User::factory()->create();

    $this->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('a.jpg')]])
        ->assertRedirect('/login');
    $this->actingAs($cliente)
        ->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('a.jpg')]])
        ->assertForbidden();
    $this->actingAs($cliente)->delete(route('admin.obras.imagenes.destroy', [$obra, $imagen]))->assertForbidden();
    $this->actingAs($cliente)->patch(route('admin.obras.imagenes.principal', [$obra, $imagen]))->assertForbidden();

    expect($obra->imagenes()->count())->toBe(1);
});

test('una imagen solo se toca desde la URL de su propia obra', function () {
    $imagen = ObraImagen::factory()->create();
    $otra = Product::factory()->create();

    $this->actingAs(admin())->delete(route('admin.obras.imagenes.destroy', [$otra, $imagen]))->assertNotFound();
    expect($imagen->fresh())->not->toBeNull();
});

// --- Ordenar, elegir la principal y borrar ---

test('el admin cambia el orden de las imágenes', function () {
    $obra = obraConImagen();
    [$primera, $segunda, $tercera] = ObraImagen::factory()->count(3)->for($obra, 'obra')->create();
    $admin = admin();

    $this->actingAs($admin)
        ->patch(route('admin.obras.imagenes.mover', [$obra, $tercera]), ['direccion' => 'arriba'])
        ->assertRedirect();
    expect($obra->imagenes()->pluck('id')->all())->toBe([$primera->id, $tercera->id, $segunda->id]);

    // La primera no puede subir más
    $this->actingAs($admin)->patch(route('admin.obras.imagenes.mover', [$obra, $primera]), ['direccion' => 'arriba']);
    expect($obra->imagenes()->pluck('id')->all())->toBe([$primera->id, $tercera->id, $segunda->id]);

    $this->actingAs($admin)
        ->patch(route('admin.obras.imagenes.mover', [$obra, $primera]), ['direccion' => 'lateral'])
        ->assertSessionHasErrors('direccion');
});

test('el orden de una obra no mezcla imágenes de otras obras', function () {
    $obra = obraConImagen();
    $ajena = ObraImagen::factory()->create();
    $propia = ObraImagen::factory()->for($obra, 'obra')->create();

    expect($propia->sort_order)->toBe(1)->and($ajena->sort_order)->toBe(1);

    $this->actingAs(admin())->patch(route('admin.obras.imagenes.mover', [$obra, $propia]), ['direccion' => 'abajo']);
    expect($ajena->fresh()->sort_order)->toBe(1);
});

test('una imagen adicional pasa a ser la principal y la principal ocupa su sitio, con sus miniaturas', function () {
    $obra = obraConImagen();
    app(GenerarMiniaturas::class)->handle($obra);
    $this->actingAs(admin())->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('d.jpg', 700, 500)]]);
    $imagen = $obra->imagenes()->sole();
    $fichero = $imagen->imagen;

    $this->actingAs(admin())
        ->patch(route('admin.obras.imagenes.principal', [$obra, $imagen]))
        ->assertRedirect()
        ->assertSessionHas('success', 'Imagen principal cambiada');

    $obra->refresh();
    $imagen->refresh();
    expect($obra->imagen)->toBe($fichero)
        ->and($imagen->imagen)->toBe('principal.jpg')
        ->and($obra->anchosMiniaturas())->toBe([400, 700])
        ->and($imagen->anchosMiniaturas())->toBe([400, 800, 900]);
});

test('al borrar una imagen se borran su fichero y sus miniaturas, y la principal sigue', function () {
    $obra = obraConImagen();
    $this->actingAs(admin())->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('d.jpg', 700, 500)]]);
    $imagen = $obra->imagenes()->sole();

    $this->actingAs(admin())
        ->delete(route('admin.obras.imagenes.destroy', [$obra, $imagen]))
        ->assertRedirect(route('admin.obras.edit', $obra).'#imagenes');

    expect(ObraImagen::count())->toBe(0);
    Storage::disk('public')->assertMissing('obras/'.$imagen->imagen);
    Storage::disk('public')->assertMissing($imagen->carpetaMiniaturas());
    Storage::disk('public')->assertExists('obras/principal.jpg');
});

test('al eliminar la obra definitivamente se van también sus imágenes; en la papelera se conservan', function () {
    $obra = obraConImagen();
    $this->actingAs(admin())->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('d.jpg', 700, 500)]]);
    $imagen = $obra->imagenes()->sole();

    $obra->delete();
    expect($imagen->fresh())->not->toBeNull();
    Storage::disk('public')->assertExists('obras/'.$imagen->imagen);

    $obra->forceDelete();
    expect(ObraImagen::count())->toBe(0);
    Storage::disk('public')->assertMissing('obras/'.$imagen->imagen);
    Storage::disk('public')->assertMissing($imagen->carpetaMiniaturas());
    Storage::disk('public')->assertMissing('obras/principal.jpg');
});

// --- Pantallas y API ---

test('la pantalla de edición lista las imágenes con sus acciones', function () {
    $obra = obraConImagen();
    $imagen = ObraImagen::factory()->for($obra, 'obra')->create();

    $this->actingAs(admin())->get(route('admin.obras.edit', $obra))
        ->assertOk()
        ->assertSee('Más imágenes')
        ->assertSee('name="imagenes[]"', false)
        ->assertSee(route('admin.obras.imagenes.principal', [$obra, $imagen]), false)
        ->assertSee(route('admin.obras.imagenes.destroy', [$obra, $imagen]), false);
});

test('con el máximo de imágenes ya no se ofrece subir más', function () {
    $obra = obraConImagen();
    ObraImagen::factory()->count(SubirImagenesObraRequest::MAXIMO_POR_OBRA)->for($obra, 'obra')->create();

    $this->actingAs(admin())->get(route('admin.obras.edit', $obra))
        ->assertOk()
        ->assertDontSee('name="imagenes[]"', false);
});

test('la ficha enseña todas las imágenes en orden y el visor pasa de una a otra', function () {
    $obra = obraConImagen();
    $segunda = ObraImagen::factory()->for($obra, 'obra')->create(['sort_order' => 2]);
    $primera = ObraImagen::factory()->for($obra, 'obra')->create(['sort_order' => 1]);

    $this->get(route('obras.show', $obra->slug))
        ->assertOk()
        ->assertSeeInOrder([$obra->imagenUrl(), $primera->miniaturaUrl(), $segunda->miniaturaUrl()], false)
        ->assertSee('data-lightbox-indice="2"', false)
        ->assertSee('data-lightbox-siguiente', false)
        ->assertSee('1 / 3');
});

test('una obra con una sola imagen se ve como antes, sin miniaturas ni flechas', function () {
    $obra = obraConImagen();

    $this->get(route('obras.show', $obra->slug))
        ->assertOk()
        ->assertDontSee('data-lightbox-indice="1"', false)
        ->assertDontSee('data-lightbox-siguiente', false);
});

test('la API da las imágenes adicionales de cada obra y se renueva al añadir una', function () {
    $obra = obraConImagen();

    expect($this->getJson('/api/products')->json('data.0.imagenes'))->toBe([]);

    $this->actingAs(admin())->post(route('admin.obras.imagenes.store', $obra), ['imagenes' => [UploadedFile::fake()->image('d.jpg', 700, 500)]]);
    $imagen = $obra->imagenes()->sole();

    $this->getJson('/api/products')->assertJsonPath('data.0.imagenes.0.imagen_url', $imagen->imagenUrl());
    $this->getJson("/api/products/{$obra->id}")->assertJsonPath('data.imagenes.0.srcset', $imagen->srcset());
});

test('obras:miniaturas también genera las de las imágenes adicionales', function () {
    $obra = obraConImagen();
    $imagen = ObraImagen::factory()->for($obra, 'obra')->create(['imagen' => 'extra.jpg']);
    Storage::disk('public')->put('obras/extra.jpg', UploadedFile::fake()->image('extra.jpg', 1200, 800)->getContent());

    $this->artisan('obras:miniaturas')
        ->expectsOutputToContain("{$obra->name} (imagen adicional extra.jpg): 400, 800, 1200 px")
        ->assertSuccessful();

    expect($imagen->anchosMiniaturas())->toBe([400, 800, 1200]);
});
