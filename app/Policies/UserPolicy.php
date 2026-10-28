<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Solo un admin puede listar todos los usuarios
    public function viewAny(User $authUser): bool
    {
        return $authUser->role === 'admin';
    }

    // Cada usuario puede ver su propia cuenta; el admin, cualquiera
    public function view(User $authUser, User $user): bool
    {
        return $authUser->role === 'admin' || $authUser->id === $user->id;
    }

    // Crear cuentas desde la API es cosa del admin (el registro público va por Auth::routes)
    public function create(User $authUser): bool
    {
        return $authUser->role === 'admin';
    }

    // Cada usuario puede editar sus datos; el admin, los de cualquiera
    public function update(User $authUser, User $user): bool
    {
        return $authUser->role === 'admin' || $authUser->id === $user->id;
    }

    // Cambiar el rol solo lo puede hacer un admin, y nunca sobre sí mismo
    // (así no puede quitarse el rol por error y dejar la app sin admin)
    public function changeRole(User $authUser, User $user): bool
    {
        return $authUser->role === 'admin' && $authUser->id !== $user->id;
    }

    // Borrar cuentas solo lo puede hacer un admin, y nunca la suya propia
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->role === 'admin' && $authUser->id !== $user->id;
    }
}
