<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Intent "editar página" del admin: título, texto y, según la página, foto o datos de contacto
class UpdatePaginaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('pagina')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $reglas = [
            'titulo' => 'required|string|max:100',
            'contenido' => 'required|string|max:10000',
        ];

        if ($this->route('pagina')->esContacto()) {
            return $reglas + [
                'email' => 'nullable|email|max:255',
                'telefono' => ['nullable', 'string', 'max:30', 'regex:/^\+?[\d\s().-]{6,}$/'],
                'instagram' => ['nullable', 'url:https', 'max:255', 'regex:#^https://(www\.)?instagram\.com/#'],
                'ubicacion' => 'nullable|string|max:255',
            ];
        }

        return $reglas + [
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'quitar_foto' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'contenido' => 'texto',
            'telefono' => 'teléfono',
            'ubicacion' => 'ubicación',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'instagram.regex' => 'El enlace de Instagram debe empezar por https://instagram.com/ o https://www.instagram.com/.',
            'telefono.regex' => 'El teléfono solo puede tener números, espacios, guiones, paréntesis y un + al principio.',
        ];
    }
}
