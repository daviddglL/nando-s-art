<?php

namespace App\Enums;

// Tema de una obra o de un encargo
enum Category: string
{
    use HasValues;

    case Retrato = 'retrato';
    case Paisaje = 'paisaje';
    case Abstracto = 'abstracto';
    case Urbano = 'urbano';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Retrato => 'Retrato',
            self::Paisaje => 'Paisaje',
            self::Abstracto => 'Abstracto',
            self::Urbano => 'Urbano',
            self::Otro => 'Otro',
        };
    }

    // Color del texto en la tabla de presupuestos del admin
    public function textColor(): string
    {
        return match ($this) {
            self::Retrato => 'text-pink-600',
            self::Paisaje => 'text-green-600',
            self::Abstracto => 'text-indigo-600',
            self::Urbano => 'text-blue-600',
            self::Otro => 'text-gray-600',
        };
    }
}
