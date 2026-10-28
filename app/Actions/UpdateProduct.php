<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProduct
{
    public function __construct(private GenerarMiniaturas $miniaturas) {}

    /**
     * Guarda los cambios de una obra. Si llega una imagen nueva, sustituye a la anterior,
     * borra el fichero viejo con sus miniaturas y genera las nuevas.
     *
     * @param  array<string, mixed>  $data  datos ya validados por UpdateProductRequest
     */
    public function handle(Product $obra, array $data, ?UploadedFile $imagen = null): Product
    {
        // Copia con la imagen vieja, para poder borrar después su fichero y sus miniaturas
        $anterior = $obra->replicate();

        if ($imagen) {
            $data['imagen'] = basename((string) $imagen->store('obras', 'public'));
        }

        $obra->update($data);

        if ($imagen) {
            if ($anterior->imagen) {
                Storage::disk('public')->delete('obras/'.$anterior->imagen);
                $anterior->borrarMiniaturas();
            }
            $this->miniaturas->handle($obra);
        }

        return $obra;
    }
}
