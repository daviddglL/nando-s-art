<?php

namespace App\Actions;

use App\Enums\EstadoEncargo;
use App\Enums\Role;
use App\Models\Encargo;
use App\Models\User;
use App\Notifications\NuevoPresupuesto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class SolicitarPresupuesto
{
    /**
     * Crea un encargo pendiente a nombre del cliente, con su imagen de referencia si la hay, y avisa al artista.
     *
     * @param  array<string, mixed>  $data  datos ya validados por StoreEncargoRequest
     */
    public function handle(User $cliente, array $data, ?UploadedFile $imagen = null): Encargo
    {
        $data['imagen'] = $imagen?->store('presupuestos', Encargo::DISCO_IMAGENES) ?: null;

        // El encargo siempre pertenece al cliente que lo pide, nunca a un user_id enviado en el formulario
        unset($data['user_id']);

        $encargo = $cliente->encargos()->create($data + ['status' => EstadoEncargo::Pending]);

        // Si el propio admin crea el encargo no hace falta avisarle
        $admins = User::where('role', Role::Admin)->whereKeyNot($cliente->id)->get();
        Notification::send($admins, new NuevoPresupuesto($encargo));

        return $encargo;
    }
}
