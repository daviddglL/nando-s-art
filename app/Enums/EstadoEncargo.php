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
            self::Pending => 'bg-acento-claro text-acento-oscuro',
            self::Reviewed => 'bg-[#dde8ee] text-[#21465a]',
            self::Completed => 'bg-salvia/15 text-[#3e5443]',
        };
    }
}
