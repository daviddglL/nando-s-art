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

    // Tinte del papel de la galería cuando este estilo está seleccionado
    public function background(): string
    {
        return match ($this) {
            self::Acuarela => 'bg-[#e6eef1]',
            self::Rotulador => 'bg-[#f2ecd6]',
            self::Oleo => 'bg-[#f2e1da]',
            self::Sketch => 'bg-[#eae8e3]',
            self::Papel => 'bg-[#e5ece0]',
            self::Lienzo => 'bg-[#ebe4ee]',
        };
    }

    // Clases del botón de filtro cuando está activo: un tono oscuro del mismo color que el tinte
    public function buttonClasses(): string
    {
        return match ($this) {
            self::Acuarela => 'bg-[#2c5870] text-white hover:bg-[#21465a]',
            self::Rotulador => 'bg-[#76590f] text-white hover:bg-[#5e470b]',
            self::Oleo => 'bg-[#8c3d1f] text-white hover:bg-[#713118]',
            self::Sketch => 'bg-[#3a3732] text-white hover:bg-[#25231f]',
            self::Papel => 'bg-[#44603d] text-white hover:bg-[#354c30]',
            self::Lienzo => 'bg-[#5a3e6a] text-white hover:bg-[#473153]',
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
