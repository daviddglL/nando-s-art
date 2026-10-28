<?php

namespace App\Http\Requests;

use App\Enums\Category;
use App\Enums\Style;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Intent "crear obra": valida los datos y comprueba el permiso con ProductPolicy
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    // Las casillas sin marcar no llegan en el formulario: se convierten en false
    protected function prepareForValidation(): void
    {
        $this->merge([
            'available' => $this->boolean('available'),
            'featured' => $this->boolean('featured'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'price' => 'nullable|numeric|min:0|max:99999999',
            'available' => 'boolean',
            'featured' => 'boolean',
            'height' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'style' => ['required', Rule::enum(Style::class)],
            'category' => ['required', Rule::enum(Category::class)],
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
