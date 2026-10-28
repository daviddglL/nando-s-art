<?php

namespace App\Http\Requests;

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
            'description' => 'required|string',
            'height' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'style' => ['required', Rule::enum(Style::class)],
            'category' => ['required', Rule::enum(Category::class)],
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
