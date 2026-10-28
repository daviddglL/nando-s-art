<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

// Intent "añadir imágenes a una obra": varias a la vez, sin pasar del máximo por obra
class SubirImagenesObraRequest extends FormRequest
{
    public const MAXIMO_POR_OBRA = 12;

    // Sus errores salen en la sección de imágenes, no encima del formulario de la obra
    protected $errorBag = 'imagenes';

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('obra')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Product $obra */
        $obra = $this->route('obra');
        $quedan = max(self::MAXIMO_POR_OBRA - $obra->imagenes()->count(), 0);

        return [
            'imagenes' => ['required', 'array', 'max:'.$quedan],
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'imagenes.max' => 'Cada obra puede tener como mucho '.self::MAXIMO_POR_OBRA.' imágenes adicionales.',
        ];
    }
}
