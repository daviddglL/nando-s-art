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
            'name' => $this->name,
            'description' => $this->description,
            'height' => $this->height,
            'width' => $this->width,
            'style' => $this->style->value,
            'style_label' => $this->style->label(),
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'imagen_url' => asset('storage/obras/'.rawurlencode((string) $this->imagen)),
        ];
    }
}
