<?php

namespace App\Http\Requests;

use App\Enums\EstadoEncargo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Intent "responder presupuesto" del admin: estado, mensaje y precio orientativo
class UpdateEncargoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('encargo')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(EstadoEncargo::class)],
            'respuesta' => 'nullable|string|max:5000',
            'precio' => 'nullable|numeric|min:0|max:99999999',
        ];
    }
}
