<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Desde que se exige el email verificado para pedir presupuestos, las cuentas que ya existían
    // se dan por verificadas: si no, sus dueños (y el admin) se quedarían sin acceso de un día para otro.
    public function up(): void
    {
        DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // No se puede saber qué cuentas estaban sin verificar antes
    }
};
