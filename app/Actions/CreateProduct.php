<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

class CreateProduct
{
    /**
     * Guarda la imagen y crea la obra.
     *
     * @param  array<string, mixed>  $data  datos ya validados por StoreProductRequest
     */
    public function handle(array $data, UploadedFile $imagen): Product
    {
        // Nombre aleatorio: el cliente no decide el nombre y dos obras nunca comparten imagen.
        // Se guarda en storage/app/public/obras (visible en /storage/obras con `php artisan storage:link`)
        $data['imagen'] = basename((string) $imagen->store('obras', 'public'));

        return Product::create($data);
    }
}
