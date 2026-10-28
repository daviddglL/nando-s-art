<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Textos de las páginas "Sobre mí" y "Contacto", editables desde /admin/paginas.
    // Las filas se crean solas la primera vez que se pide cada página (Pagina::obtener).
    public function up(): void
    {
        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('titulo');
            $table->text('contenido');
            $table->string('foto')->nullable();
            // Datos de contacto: solo los usa la página de contacto
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('instagram')->nullable();
            $table->string('ubicacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas');
    }
};
