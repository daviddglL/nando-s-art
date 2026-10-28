<?php

namespace App\Policies;

use App\Models\Pagina;
use App\Models\User;

class PaginaPolicy
{
    // Leer las páginas es público; editarlas es solo del admin
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Pagina $pagina): bool
    {
        return $user->isAdmin();
    }
}
