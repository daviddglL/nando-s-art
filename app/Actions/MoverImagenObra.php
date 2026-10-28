<?php

namespace App\Actions;

use App\Models\ObraImagen;
use Illuminate\Support\Facades\DB;

class MoverImagenObra
{
    /**
     * Intercambia la posición de una imagen adicional con la de su vecina dentro de la misma obra.
     *
     * @param  'arriba'|'abajo'  $direccion
     */
    public function handle(ObraImagen $imagen, string $direccion): void
    {
        $vecina = ObraImagen::query()
            ->where('product_id', $imagen->product_id)
            ->when(
                $direccion === 'arriba',
                fn ($q) => $q->where('sort_order', '<', $imagen->sort_order)->orderByDesc('sort_order'),
                fn ($q) => $q->where('sort_order', '>', $imagen->sort_order)->orderBy('sort_order'),
            )
            ->first();

        // Ya es la primera o la última
        if (! $vecina) {
            return;
        }

        DB::transaction(function () use ($imagen, $vecina) {
            [$imagen->sort_order, $vecina->sort_order] = [$vecina->sort_order, $imagen->sort_order];
            $imagen->save();
            $vecina->save();
        });
    }
}
