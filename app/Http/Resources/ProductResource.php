<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Forma estable de una obra para el front (el estado que recibe la galería).
 *
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'year' => $this->year,
            'price' => $this->price,
            'precio_texto' => $this->precioTexto(),
            'available' => $this->available,
            'featured' => $this->featured,
            'height' => $this->height,
            'width' => $this->width,
            'style' => $this->style->value,
            'style_label' => $this->style->label(),
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'imagen_url' => $this->imagenUrl(),
            // Miniaturas WebP para que el navegador baje solo el tamaño que necesita (null si no hay)
            'srcset' => $this->srcset(),
            'url' => route('obras.show', $this->slug),
        ];
    }
}
