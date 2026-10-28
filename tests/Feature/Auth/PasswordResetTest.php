<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('se muestra la pantalla para pedir el enlace', function () {
    $this->get('/password/reset')
        ->assertOk()
        ->assertSee('Recuperar contraseña');
});

test('se envía el enlace para restablecer la contraseña', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/password/email', ['email' => $user->email])
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('se muestra la pantalla para elegir la contraseña nueva', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/password/email', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $this->get('/password/reset/'.$notification->token)
            ->assertOk()
            ->assertSee('Nueva contraseña');

        return true;
    });
});

test('la contraseña se cambia con un token válido', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/password/email', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->post('/password/reset', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])->assertSessionHasNoErrors()->assertRedirect('/dashboard');

        return true;
    });

    expect(Hash::check('nueva-clave-123', $user->fresh()->password))->toBeTrue();
});
