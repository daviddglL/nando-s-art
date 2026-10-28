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

    // Etiqueta de color en las listas de presupuestos
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-100 text-yellow-900',
            self::Reviewed => 'bg-blue-100 text-blue-900',
            self::Completed => 'bg-green-100 text-green-900',
        };
    }
}
