<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Intent "escribir al artista" desde /contacto, sin cuenta
class EnviarMensajeContactoRequest extends FormRequest
{
    // Cualquiera puede escribir: el límite está en el throttle 'contacto' y en el campo trampa
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'mensaje' => 'required|string|min:10|max:5000',
            // Campo trampa (honeypot): está oculto, así que solo lo rellenan los bots
            'web' => 'prohibited',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['nombre' => 'nombre', 'mensaje' => 'mensaje'];
    }
}
