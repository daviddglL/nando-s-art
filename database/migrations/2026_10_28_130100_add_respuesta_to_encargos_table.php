<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Respuesta del artista a un presupuesto: mensaje, precio orientativo y cuándo se respondió
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('encargos', function (Blueprint $table) {
            $table->text('respuesta')->nullable()->after('status');
            $table->decimal('precio', 10, 2)->nullable()->after('respuesta');
            $table->timestamp('respondido_at')->nullable()->after('precio');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('encargos', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['respuesta', 'precio', 'respondido_at']);
        });
    }
};
