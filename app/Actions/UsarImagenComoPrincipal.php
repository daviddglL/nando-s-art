<?php

namespace App\Actions;

use App\Models\ObraImagen;
use Illuminate\Support\Facades\DB;

class UsarImagenComoPrincipal
{
    /**
     * Cambia la imagen principal de la obra por una adicional: la principal de antes ocupa su sitio.
     * Solo se intercambian los nombres de fichero; las miniaturas van con su fichero y no hay que regenerarlas.
     */
    public function handle(ObraImagen $imagen): void
    {
        $obra = $imagen->obra;

        DB::transaction(function () use ($obra, $imagen) {
            [$obra->imagen, $imagen->imagen] = [$imagen->imagen, $obra->imagen];
            $obra->save();
            $imagen->save();
        });
    }
}
