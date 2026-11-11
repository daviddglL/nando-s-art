<?php

namespace App\Http\Requests;

use App\Actions\GuardarImagen;
use App\Enums\Category;
use App\Enums\Style;
use App\Models\Encargo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Intent "solicitar presupuesto": valida los datos y comprueba el permiso con EncargoPolicy
class StoreEncargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Encargo::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Con máximo: el texto llega por email al artista y nadie encarga un cuadro de diez kilómetros
            'description' => 'required|string|max:5000',
            'height' => 'required|numeric|min:1|max:1000',
            'width' => 'required|numeric|min:1|max:1000',
            'style' => ['required', Rule::enum(Style::class)],
            'category' => ['required', Rule::enum(Category::class)],
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:'.GuardarImagen::MAX_KB,
        ];
    }
}
