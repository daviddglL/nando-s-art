<?php

use App\Enums\Role;
use App\Models\User;

test('un invitado no puede acceder a la API de usuarios', function () {
    $user = User::factory()->create();

    $this->getJson('/api/users')->assertUnauthorized();
    $this->putJson("/api/users/{$user->id}", ['role' => 'admin'])->assertUnauthorized();
    $this->deleteJson("/api/users/{$user->id}")->assertUnauthorized();

    expect($user->fresh()->role)->toBe(Role::User);
});

test('un usuario normal no puede ascenderse a admin', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['role' => 'admin'])
        ->assertForbidden();

    expect($user->fresh()->role)->toBe(Role::User);
});

test('un usuario normal no puede editar ni borrar a otros', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->putJson("/api/users/{$other->id}", ['name' => 'Hackeado'])->assertForbidden();
    $this->actingAs($user)->deleteJson("/api/users/{$other->id}")->assertForbidden();
    $this->actingAs($user)->deleteJson("/api/users/{$user->id}")->assertForbidden();
    $this->actingAs($user)->getJson('/api/users')->assertForbidden();

    expect(User::find($other->id))->not->toBeNull();
});

test('un usuario normal puede editar su nombre pero no campos no permitidos', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/users/{$user->id}", ['name' => 'Nuevo nombre', 'email_verified_at' => null])
        ->assertOk();

    expect($user->fresh()->name)->toBe('Nuevo nombre')
        ->and($user->fresh()->email_verified_at)->not->toBeNull();
});

test('un admin puede cambiar roles y borrar usuarios', function () {
    $admin = admin();
    $user = User::factory()->create();

    $this->actingAs($admin)->getJson('/api/users')->assertOk();
    $this->actingAs($admin)->putJson("/api/users/{$user->id}", ['role' => 'admin'])->assertOk();
    expect($user->fresh()->role)->toBe(Role::Admin);

    $this->actingAs($admin)->deleteJson("/api/users/{$user->id}")->assertOk();
    expect(User::find($user->id))->toBeNull();
});

test('un admin no puede quitarse el rol ni borrarse a sí mismo', function () {
    $admin = admin();

    $this->actingAs($admin)->putJson("/api/users/{$admin->id}", ['role' => 'user'])->assertForbidden();
    $this->actingAs($admin)->deleteJson("/api/users/{$admin->id}")->assertForbidden();

    expect($admin->fresh()->role)->toBe(Role::Admin);
});

test('la respuesta nunca incluye la contraseña', function () {
    $admin = admin();

    $this->actingAs($admin)->getJson('/api/users')
        ->assertOk()
        ->assertJsonMissingPath('0.password');
});
