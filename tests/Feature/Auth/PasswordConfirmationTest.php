<?php

use App\Models\User;

test('se muestra la pantalla de confirmar contraseña', function () {
    $this->actingAs(User::factory()->create())
        ->get('/password/confirm')
        ->assertOk()
        ->assertSee('Confirmar contraseña');
});

test('la contraseña se confirma', function () {
    $this->actingAs(User::factory()->create())
        ->post('/password/confirm', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('auth.password_confirmed_at');
});

test('no se confirma con una contraseña incorrecta', function () {
    $this->actingAs(User::factory()->create())
        ->post('/password/confirm', ['password' => 'contraseña-incorrecta'])
        ->assertSessionHasErrors('password');
});
