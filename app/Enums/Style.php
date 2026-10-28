<?php

namespace App\Enums;

// Técnica de una obra. Es la única lista de estilos: migración, validación, vistas y seeders salen de aquí.
enum Style: string
{
    use HasValues;

    case Acuarela = 'acuarela';
    case Rotulador = 'rotulador';
    case Oleo = 'oleo';
    case Sketch = 'sketch';
    case Papel = 'papel';
    case Lienzo = 'lienzo';

    public function label(): string
    {
        return match ($this) {
            self::Acuarela => 'Acuarela',
            self::Rotulador => 'Rotulador',
            self::Oleo => 'Óleo',
            self::Sketch => 'Sketch',
            self::Papel => 'Papel',
            self::Lienzo => 'Lienzo',
        };
    }

    // Fondo pastel de la galería cuando este estilo está seleccionado
    public function background(): string
    {
        return match ($this) {
            self::Acuarela => 'bg-blue-100',
            self::Rotulador => 'bg-yellow-100',
            self::Oleo => 'bg-pink-100',
            self::Sketch => 'bg-gray-100',
            self::Papel => 'bg-green-100',
            self::Lienzo => 'bg-purple-100',
        };
    }

    // Clases del botón de filtro cuando está activo
    public function buttonClasses(): string
    {
        return match ($this) {
            self::Acuarela => 'bg-blue-100 text-blue-900 hover:bg-blue-200',
            self::Rotulador => 'bg-yellow-100 text-yellow-900 hover:bg-yellow-200',
            self::Oleo => 'bg-pink-100 text-pink-900 hover:bg-pink-200',
            self::Sketch => 'bg-gray-100 text-gray-900 hover:bg-gray-200',
            self::Papel => 'bg-green-100 text-green-900 hover:bg-green-200',
            self::Lienzo => 'bg-purple-100 text-purple-900 hover:bg-purple-200',
        };
    }

    // Color del texto en la tabla de presupuestos del admin
    public function textColor(): string
    {
        return match ($this) {
            self::Acuarela => 'text-sky-600',
            self::Rotulador => 'text-yellow-600',
            self::Oleo => 'text-pink-600',
            self::Sketch => 'text-gray-600',
            self::Papel => 'text-emerald-600',
            self::Lienzo => 'text-purple-600',
        };
    }
}
