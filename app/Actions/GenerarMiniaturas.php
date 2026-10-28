<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class GenerarMiniaturas
{
    // Anchos que se generan; las imágenes más estrechas solo se convierten a WebP con su ancho real
    public const ANCHOS = [400, 800, 1600];

    /**
     * Crea las versiones WebP de la imagen de una obra en storage/app/public/obras/miniaturas/{nombre}/{ancho}.webp.
     * Devuelve los anchos generados (vacío si no hay imagen o el servidor no tiene GD con WebP).
     *
     * @return list<int>
     */
    public function handle(Product $obra): array
    {
        $disco = Storage::disk('public');
        $ruta = 'obras/'.$obra->imagen;

        if (! $obra->imagen || ! $disco->exists($ruta) || ! function_exists('imagewebp')) {
            return [];
        }

        $original = @imagecreatefromstring((string) $disco->get($ruta));
        if ($original === false) {
            return [];
        }
        // Los GIF y PNG con paleta no se pueden guardar como WebP sin pasarlos antes a color real
        if (! imageistruecolor($original)) {
            imagepalettetotruecolor($original);
        }

        $carpeta = $obra->carpetaMiniaturas();
        $disco->deleteDirectory($carpeta);

        $anchoOriginal = imagesx($original);
        $anchos = array_values(array_unique(array_map(fn (int $ancho) => min($ancho, $anchoOriginal), self::ANCHOS)));

        foreach ($anchos as $ancho) {
            $miniatura = imagescale($original, $ancho);
            if ($miniatura === false) {
                continue;
            }

            ob_start();
            imagewebp($miniatura, null, 80);
            $disco->put("{$carpeta}/{$ancho}.webp", (string) ob_get_clean());
            imagedestroy($miniatura);
        }

        imagedestroy($original);

        return $anchos;
    }
}
