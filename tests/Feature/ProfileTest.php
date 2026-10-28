<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('se muestra la página del perfil', function () {
    $this->actingAs(User::factory()->create())
        ->get('/profile')
        ->assertOk()
        ->assertSee('Datos de la cuenta')
        ->assertSee('Cambiar contraseña')
        ->assertSee('Borrar la cuenta');
});

test('un invitado no puede ver el perfil', function () {
    $this->get('/profile')->assertRedirect('/login');
});

test('se actualizan el nombre y el email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'Nombre Nuevo', 'email' => 'nuevo@example.com'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    expect($user->name)->toBe('Nombre Nuevo');
    expect($user->email)->toBe('nuevo@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('si el email no cambia sigue verificado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', ['name' => 'Nombre Nuevo', 'email' => $user->email])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('se cambia la contraseña dando la actual', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect(Hash::check('nueva-clave-123', $user->refresh()->password))->toBeTrue();
});

test('no se cambia la contraseña si la actual es incorrecta', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put('/profile/password', [
            'current_password' => 'contraseña-incorrecta',
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])
        ->assertSessionHasErrorsIn('updatePassword', 'current_password');

    expect(Hash::check('password', $user->refresh()->password))->toBeTrue();
});

test('el usuario puede borrar su cuenta', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('para borrar la cuenta hay que dar la contraseña correcta', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'contraseña-incorrecta'])
        ->assertSessionHasErrorsIn('deleteUser', 'password');

    expect($user->fresh())->not->toBeNull();
});
