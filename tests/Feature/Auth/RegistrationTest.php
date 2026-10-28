<?php

use App\Enums\Role;
use App\Models\User;

test('se muestra la pantalla de registro', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('Registro de Usuario');
});

test('un visitante se registra y queda con rol de usuario', function () {
    $this->post('/register', [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        // Aunque lo intente, el rol no se asigna desde el formulario
        'role' => 'admin',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticated();
    expect(User::where('email', 'ana@example.com')->first()->role)->toBe(Role::User);
});

test('el registro exige confirmar la contraseña', function () {
    $this->post('/register', [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'password123',
        'password_confirmation' => 'otra-cosa',
    ])->assertSessionHasErrors('password');

    $this->assertGuest();
});
