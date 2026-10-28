<?php

namespace App\Actions;

use App\Models\ObraImagen;
use App\Models\Product;
use Illuminate\Http\UploadedFile;

class SubirImagenesObra
{
    public function __construct(private GenerarMiniaturas $miniaturas) {}

    /**
     * Añade imágenes adicionales al final de las de la obra, cada una con sus miniaturas WebP.
     *
     * @param  list<UploadedFile>  $ficheros
     * @return list<ObraImagen>
     */
    public function handle(Product $obra, array $ficheros): array
    {
        return array_map(function (UploadedFile $fichero) use ($obra) {
            // Nombre aleatorio, igual que la imagen principal
            $imagen = $obra->imagenes()->create([
                'imagen' => basename((string) $fichero->store('obras', 'public')),
            ]);
            $this->miniaturas->handle($imagen);

            return $imagen;
        }, $ficheros);
    }
}
