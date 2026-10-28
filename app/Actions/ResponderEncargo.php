<?php

namespace App\Actions;

use App\Models\Encargo;
use App\Notifications\PresupuestoActualizado;

class ResponderEncargo
{
    /**
     * El admin cambia el estado, responde o pone precio a un encargo. Si algo cambia, se avisa al cliente por email.
     *
     * @param  array<string, mixed>  $data  datos ya validados por UpdateEncargoRequest
     */
    public function handle(Encargo $encargo, array $data): Encargo
    {
        $encargo->fill($data);

        if ($encargo->isDirty('respuesta') && filled($encargo->respuesta)) {
            $encargo->respondido_at = now();
        }

        if (! $encargo->isDirty(['status', 'respuesta', 'precio'])) {
            return $encargo;
        }

        $encargo->save();
        $encargo->user?->notify(new PresupuestoActualizado($encargo));

        return $encargo;
    }
}
