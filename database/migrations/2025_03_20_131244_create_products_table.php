<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {


        Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->longText('description');
                $table->string('measures');
                $table->enum('style', ['acuarela', 'rotulador','oleo','sketch','papel','lienzo']);
                $table->string('imagen');
                $table->enum('category', ['retrato', 'paisaje','abstracto','urbano','otro']);
                $table->timestamps();
                $table->softDeletes();
        });
        
        
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
