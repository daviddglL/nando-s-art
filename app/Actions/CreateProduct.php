<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

class CreateProduct
{
    public function __construct(private GenerarMiniaturas $miniaturas, private GuardarImagen $guardar) {}

    /**
     * Guarda la imagen, crea la obra y genera sus miniaturas WebP.
     *
     * @param  array<string, mixed>  $data  datos ya validados por StoreProductRequest
     */
    public function handle(array $data, UploadedFile $imagen): Product
    {
        // Nombre aleatorio: el cliente no decide el nombre y dos obras nunca comparten imagen.
        // Se guarda reducida (GuardarImagen) en storage/app/public/obras (visible en /storage/obras con `php artisan storage:link`)
        $data['imagen'] = $this->guardar->handle($imagen, 'obras');

        $obra = Product::create($data);
        $this->miniaturas->handle($obra);

        return $obra;
    }
}
