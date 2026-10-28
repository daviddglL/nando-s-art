<?php

use App\Models\User;

test('se muestra la pantalla de login', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Iniciar Sesión');
});

test('un usuario inicia sesión y va a su dashboard', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('con una contraseña incorrecta no se inicia sesión', function () {
    $user = User::factory()->create();

    $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'contraseña-incorrecta',
    ])->assertRedirect('/login')->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('el menú muestra las opciones del usuario con sesión iniciada', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Mis presupuestos')
        ->assertSee('Mi perfil')
        ->assertSee('Cerrar sesión')
        ->assertDontSee('Presupuestos recibidos');
});

test('un usuario cierra sesión', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

test('un invitado que entra al dashboard va al login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('un usuario con sesión iniciada no vuelve a ver el login', function () {
    $this->actingAs(User::factory()->create())
        ->get('/login')
        ->assertRedirect('/dashboard');
});
