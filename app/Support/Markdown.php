<?php

namespace App\Support;

final class Markdown
{
    // Texto que escribe un visitante y va dentro de un email en Markdown: se escapan los signos de Markdown
    // para que nadie pueda colar un enlace con texto engañoso ([Pulsa aquí](https://…)) ni cambiar el formato
    public static function escapar(string $texto): string
    {
        return (string) preg_replace('/([\\\\`*_{}\[\]()#+\-.!|<>~])/', '\\\\$1', $texto);
    }
}
