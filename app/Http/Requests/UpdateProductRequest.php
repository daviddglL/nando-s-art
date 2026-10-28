<?php

namespace App\Http\Requests;

// Intent "editar obra": los mismos datos que al crearla, pero la imagen nueva es opcional
class UpdateProductRequest extends StoreProductRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('obra')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    }
}
