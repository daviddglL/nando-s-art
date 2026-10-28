<?php

namespace App\Policies;

use App\Models\Encargo;
use App\Models\User;

class EncargoPolicy
{
    // La lista de todos los encargos es solo para el admin
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    // Cada cliente ve los suyos; el admin, todos
    public function view(User $user, Encargo $encargo): bool
    {
        return $user->isAdmin() || $user->id === $encargo->user_id;
    }

    // Cualquier usuario con sesión puede pedir un presupuesto
    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Encargo $encargo): bool
    {
        return $user->isAdmin();
    }
}
