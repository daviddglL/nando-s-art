<?php

use App\Models\Product;
use App\Models\User;

// --- Portada ---

test('la portada muestra las obras destacadas en el orden de la galería', function () {
    $segunda = Product::factory()->create(['name' => 'Segunda destacada', 'featured' => true, 'price' => 120]);
    $primera = Product::factory()->create(['name' => 'Primera destacada', 'featured' => true, 'available' => false]);
    Product::factory()->create(['name' => 'Obra normal']);
    $primera->update(['sort_order' => 0]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Obras destacadas')
        ->assertSeeInOrder(['Primera destacada', 'Segunda destacada'])
        ->assertSee(route('obras.show', $segunda->slug), false)
        ->assertSee('120,00 €')
        ->assertSee('Vendida')
        ->assertDontSee('Obra normal')
        ->assertSee(route('galeria'), false);
});

test('la portada no muestra la sección si no hay destacadas', function () {
    Product::factory()->create(['name' => 'Obra normal']);

    $this->get('/')->assertOk()->assertDontSee('Obras destacadas');
});

test('la portada no muestra destacadas que están en la papelera', function () {
    Product::factory()->create(['name' => 'Destacada borrada', 'featured' => true])->delete();

    $this->get('/')->assertOk()->assertDontSee('Destacada borrada')->assertDontSee('Obras destacadas');
});

test('la portada enseña como mucho MAX_DESTACADAS obras', function () {
    Product::factory()->count(Product::MAX_DESTACADAS + 2)->create(['featured' => true]);

    $html = $this->get('/')->assertOk()->getContent();

    expect(substr_count($html, 'href="'.url('/obras/')))->toBe(Product::MAX_DESTACADAS);
});

// --- Panel del admin ---

test('el admin marca y desmarca una obra como destacada desde el listado', function () {
    $obra = Product::factory()->create(['name' => 'Paisaje', 'featured' => false]);

    $this->actingAs(admin())->get('/admin/obras')
        ->assertSee(route('admin.obras.destacar', $obra), false)
        ->assertSee('☆ Destacar');

    $this->patch("/admin/obras/{$obra->id}/destacar")
        ->assertRedirect('/admin/obras')
        ->assertSessionHas('success', '«Paisaje» sale ahora en la portada');
    expect($obra->fresh()->featured)->toBeTrue();
    $this->get('/')->assertSee('Paisaje');

    $this->patch("/admin/obras/{$obra->id}/destacar")
        ->assertSessionHas('success', '«Paisaje» ya no sale en la portada');
    expect($obra->fresh()->featured)->toBeFalse();
});

test('solo el admin puede destacar obras', function () {
    $obra = Product::factory()->create(['featured' => false]);

    $this->patch("/admin/obras/{$obra->id}/destacar")->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->patch("/admin/obras/{$obra->id}/destacar")->assertForbidden();

    expect($obra->fresh()->featured)->toBeFalse();
});
