<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Los presupuestos no tienen papelera: el modelo Encargo los borra de verdad (con su imagen),
// así que la columna deleted_at que venía de la tabla shipments nunca se ha usado.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('encargos', 'deleted_at')) {
            Schema::table('encargos', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('encargos', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
