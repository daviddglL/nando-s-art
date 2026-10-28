<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    // Las obras solo las sube el admin; verlas es público (no necesita policy)
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
