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

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'height' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'style' => ['required', Rule::enum(Style::class)],
            'category' => ['required', Rule::enum(Category::class)],
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
