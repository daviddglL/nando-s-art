<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// Ficha completa de cada obra (URL propia, año, precio, disponibilidad, destacada y orden)
// e índices en los filtros de la galería
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->unsignedSmallInteger('year')->nullable()->after('description');
            // Sin precio = "precio a consultar"
            $table->decimal('price', 10, 2)->nullable()->after('year');
            $table->boolean('available')->default(true)->after('price');
            $table->boolean('featured')->default(false)->after('available');
            $table->unsignedInteger('sort_order')->default(0)->after('featured');

            $table->index('style');
            $table->index('category');
            $table->index('sort_order');
        });

        // Las obras que ya existen reciben su slug y conservan el orden en que se subieron
        $usados = [];
        foreach (DB::table('products')->orderBy('id')->get(['id', 'name']) as $obra) {
            $base = Str::slug($obra->name) ?: 'obra';
            $slug = $base;
            for ($i = 2; in_array($slug, $usados, true); $i++) {
                $slug = "{$base}-{$i}";
            }
            $usados[] = $slug;

            DB::table('products')->where('id', $obra->id)->update(['slug' => $slug, 'sort_order' => $obra->id]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['style']);
            $table->dropIndex(['category']);
            $table->dropIndex(['sort_order']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'year', 'price', 'available', 'featured', 'sort_order']);
        });
    }
};
