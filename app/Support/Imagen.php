<?php

namespace App\Support;

use GdImage;

/**
 * Utilidades de GD para las fotos de las obras: abrirlas con la orientación correcta (EXIF)
 * y con memoria suficiente para fotos grandes de cámara o móvil.
 */
final class Imagen
{
    // Bytes por píxel que necesita GD al decodificar (color real con alfa) más un margen
    private const BYTES_POR_PIXEL = 5;

    // Por encima de esto no se intenta abrir (una foto de 100 Mpx no es una foto razonable de una obra)
    public const MAX_PIXELES = 100_000_000;

    /**
     * Ancho y alto de la imagen ya girada según su EXIF (null si no es una imagen).
     *
     * @return array{0: int, 1: int}|null
     */
    public static function medidas(string $bytes): ?array
    {
        $info = @getimagesizefromstring($bytes);
        if ($info === false) {
            return null;
        }

        [$ancho, $alto] = $info;

        // Las orientaciones 5 a 8 son giros de 90 grados: el ancho y el alto se cambian
        return self::orientacion($bytes) >= 5 ? [$alto, $ancho] : [$ancho, $alto];
    }

    // Orientación EXIF (1 = normal). Solo los JPEG la llevan en la práctica.
    public static function orientacion(string $bytes): int
    {
        if (! function_exists('exif_read_data') || ! str_starts_with($bytes, "\xFF\xD8")) {
            return 1;
        }

        $flujo = fopen('php://memory', 'r+b');
        if ($flujo === false) {
            return 1;
        }
        fwrite($flujo, $bytes);
        rewind($flujo);
        $exif = @exif_read_data($flujo);
        fclose($flujo);

        $orientacion = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

        return $orientacion >= 1 && $orientacion <= 8 ? $orientacion : 1;
    }

    // Abre la imagen en color real y girada como la ve el navegador. Null si no se puede.
    public static function abrir(string $bytes): ?GdImage
    {
        $info = @getimagesizefromstring($bytes);
        if ($info === false || $info[0] * $info[1] > self::MAX_PIXELES) {
            return null;
        }

        self::asegurarMemoria($info[0] * $info[1]);

        $imagen = @imagecreatefromstring($bytes);
        if ($imagen === false) {
            return null;
        }

        // Los GIF y PNG con paleta no se pueden guardar como WebP sin pasarlos antes a color real
        if (! imageistruecolor($imagen)) {
            imagepalettetotruecolor($imagen);
        }

        return self::orientar($imagen, self::orientacion($bytes));
    }

    // Aplica la orientación EXIF: el navegador gira la original solo, pero GD no
    public static function orientar(GdImage $imagen, int $orientacion): GdImage
    {
        if (in_array($orientacion, [2, 4, 5, 7], true)) {
            imageflip($imagen, IMG_FLIP_HORIZONTAL);
        }

        $grados = match ($orientacion) {
            3, 4 => 180,
            5, 6 => 270, // imagerotate gira en sentido contrario a las agujas del reloj
            7, 8 => 90,
            default => 0,
        };

        if ($grados === 0) {
            return $imagen;
        }

        $girada = imagerotate($imagen, $grados, 0);
        if ($girada === false) {
            return $imagen;
        }
        imagedestroy($imagen);

        return $girada;
    }

    // Reduce la imagen para que su lado mayor no pase de $ladoMaximo (si ya cabe, la devuelve tal cual)
    public static function reducir(GdImage $imagen, int $ladoMaximo): GdImage
    {
        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);
        if (max($ancho, $alto) <= $ladoMaximo) {
            return $imagen;
        }

        $nuevoAncho = $ancho >= $alto ? $ladoMaximo : (int) round($ancho * $ladoMaximo / $alto);
        $reducida = imagescale($imagen, max($nuevoAncho, 1));
        if ($reducida === false) {
            return $imagen;
        }
        imagedestroy($imagen);

        return $reducida;
    }

    // Una foto de 4000×3000 ocupa unos 60 MB al decodificarla: se sube el límite solo si hace falta
    private static function asegurarMemoria(int $pixeles): void
    {
        $necesaria = $pixeles * self::BYTES_POR_PIXEL * 2 + 64 * 1024 * 1024;
        $actual = self::enBytes((string) ini_get('memory_limit'));

        if ($actual !== -1 && $actual < memory_get_usage() + $necesaria) {
            @ini_set('memory_limit', (string) min(memory_get_usage() + $necesaria, 1024 * 1024 * 1024));
        }
    }

    private static function enBytes(string $valor): int
    {
        $valor = trim($valor);
        if ($valor === '' || $valor === '-1') {
            return -1;
        }

        $numero = (int) $valor;

        return match (strtolower(substr($valor, -1))) {
            'g' => $numero * 1024 ** 3,
            'm' => $numero * 1024 ** 2,
            'k' => $numero * 1024,
            default => $numero,
        };
    }
}
