<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class MoverObra
{
    /**
     * Intercambia la posición de una obra con la de su vecina en el orden de la galería.
     *
     * @param  'arriba'|'abajo'  $direccion
     */
    public function handle(Product $obra, string $direccion): void
    {
        $vecina = Product::query()
            ->when(
                $direccion === 'arriba',
                fn ($q) => $q->where('sort_order', '<', $obra->sort_order)->orderByDesc('sort_order'),
                fn ($q) => $q->where('sort_order', '>', $obra->sort_order)->orderBy('sort_order'),
            )
            ->first();

        // Ya es la primera o la última
        if (! $vecina) {
            return;
        }

        DB::transaction(function () use ($obra, $vecina) {
            [$obra->sort_order, $vecina->sort_order] = [$vecina->sort_order, $obra->sort_order];
            $obra->save();
            $vecina->save();
        });
    }
}
