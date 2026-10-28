<?php

namespace App\Enums;

trait HasValues
{
    /**
     * Valores en bruto, en el orden en que se declaran los casos.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
