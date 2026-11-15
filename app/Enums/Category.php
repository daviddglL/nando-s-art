<?php

namespace App\Enums;

// Tema de una obra
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
}
