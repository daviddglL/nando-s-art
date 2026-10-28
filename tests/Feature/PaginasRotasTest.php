<?php

use App\Models\Encargo;
use App\Models\Product;
use App\Models\User;

test('el admin ve el menú y la lista de presupuestos', function () {
    $this->actingAs(admin())
        ->get('/admin/encargos')
        ->assertOk()
        ->assertSee('Presupuestos recibidos');
});

test('un usuario normal no puede ver la lista de presupuestos', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/encargos')
        ->assertForbidden();
});

test('un usuario envía un presupuesto y lo ve en su dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/presupuesto')->assertOk();

    $this->actingAs($user)->post('/presupuesto', [
        'description' => 'Retrato de mi perro',
        'height' => 30,
        'width' => 40,
        'style' => 'acuarela',
        'category' => 'retrato',
    ])->assertRedirect('/dashboard');

    expect($user->encargos()->count())->toBe(1);

    $this->actingAs($user)->get('/dashboard')
        ->assertOk()
        ->assertSee('Retrato de mi perro');
});

test('el admin puede borrar un presupuesto', function () {
    $encargo = User::factory()->create()->encargos()->create([
        'description' => 'x', 'height' => 1, 'width' => 1,
        'style' => 'oleo', 'category' => 'otro',
    ]);

    $this->actingAs(admin())
        ->delete("/admin/encargos/{$encargo->id}")
        ->assertRedirect('/admin/encargos');

    expect(Encargo::count())->toBe(0);
});

test('la API del carrusel filtra las obras por estilo', function () {
    $base = ['description' => 'd', 'height' => 1, 'width' => 1, 'category' => 'retrato', 'imagen' => 'a.jpg'];
    Product::create($base + ['name' => 'Acuarela 1', 'style' => 'acuarela']);
    Product::create($base + ['name' => 'Óleo 1', 'style' => 'oleo']);

    $this->getJson('/api/products?style=acuarela')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Acuarela 1');
});

test('la API de obras no permite crear sin pasar por el admin', function () {
    $this->postJson('/api/products', ['name' => 'x'])->assertStatus(405);
});
