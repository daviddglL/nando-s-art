<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProduct
{
    /**
     * Guarda los cambios de una obra. Si llega una imagen nueva, sustituye a la anterior y borra el fichero viejo.
     *
     * @param  array<string, mixed>  $data  datos ya validados por UpdateProductRequest
     */
    public function handle(Product $obra, array $data, ?UploadedFile $imagen = null): Product
    {
        $anterior = $obra->imagen;

        if ($imagen) {
            $data['imagen'] = basename((string) $imagen->store('obras', 'public'));
        }

        $obra->update($data);

        if ($imagen && $anterior) {
            Storage::disk('public')->delete('obras/'.$anterior);
        }

        return $obra;
    }
}
