<?php

namespace App\Galeria;

// Foto adicional de una obra (detalles, otros ángulos...); la principal es Obra::$imagen
final class Foto
{
    use TieneImagen;

    public function __construct(public readonly string $imagen) {}
}
