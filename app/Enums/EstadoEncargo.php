<?php

namespace App\Enums;

// Estado de un encargo (petición de presupuesto) de un cliente
enum EstadoEncargo: string
{
    use HasValues;

    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Reviewed => 'Revisado',
            self::Completed => 'Completado',
        };
    }
}
