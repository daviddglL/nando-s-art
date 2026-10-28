<?php

namespace App\Actions;

use App\Enums\EstadoEncargo;
use App\Models\Encargo;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class SolicitarPresupuesto
{
    /**
     * Crea un encargo pendiente a nombre del cliente, con su imagen de referencia si la hay.
     *
     * @param  array<string, mixed>  $data  datos ya validados por StoreEncargoRequest
     */
    public function handle(User $cliente, array $data, ?UploadedFile $imagen = null): Encargo
    {
        $data['imagen'] = $imagen?->store('presupuestos', 'public') ?: null;

        // El encargo siempre pertenece al cliente que lo pide, nunca a un user_id enviado en el formulario
        unset($data['user_id']);

        return $cliente->encargos()->create($data + ['status' => EstadoEncargo::Pending]);
    }
}
