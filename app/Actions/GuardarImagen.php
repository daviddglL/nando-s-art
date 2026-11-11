<?php

namespace App\Actions;

use App\Support\Imagen;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Guarda una foto subida con nombre aleatorio. Si es más grande de lo que la web necesita
 * (una foto de cámara de 4000 px y 4 MB) o viene girada por EXIF, se guarda ya reducida y derecha:
 * así no se sirven 4 MB por obra y las miniaturas salen bien orientadas.
 */
class GuardarImagen
{
    // Peso máximo de cada foto que se sube (15 MB): las de cámara o móvil pesan 3-8 MB.
    // El php.ini del servidor tiene que dejar pasar al menos esto (ver public/.user.ini y el README)
    public const MAX_KB = 15360;

    // Lado mayor con el que se guarda la original (las miniaturas llegan a 1600 px)
    public const LADO_MAXIMO = 2400;

    // Calidad de JPEG y WebP al volver a guardar
    private const CALIDAD = 85;

    /**
     * Devuelve el nombre del fichero guardado en {$carpeta} del disco {$disco}.
     */
    public function handle(UploadedFile $fichero, string $carpeta, string $disco = 'public'): string
    {
        $bytes = (string) file_get_contents($fichero->getRealPath());
        $optimizada = $this->optimizar($bytes, (string) $fichero->getMimeType());

        // Sin cambios (o sin GD): se guarda tal cual, como antes
        if ($optimizada === null) {
            return basename((string) $fichero->store($carpeta, $disco));
        }

        [$contenido, $extension] = $optimizada;
        $nombre = Str::random(40).'.'.$extension;
        Storage::disk($disco)->put("{$carpeta}/{$nombre}", $contenido);

        return $nombre;
    }

    /**
     * La imagen reducida y girada, o null si no hace falta tocarla.
     *
     * @return array{0: string, 1: string}|null [contenido, extensión]
     */
    public function optimizar(string $bytes, string $mime): ?array
    {
        // Los GIF pueden ser animados: GD se quedaría solo con el primer fotograma
        $formatos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (! isset($formatos[$mime]) || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $medidas = Imagen::medidas($bytes);
        if ($medidas === null) {
            return null;
        }

        $girada = Imagen::orientacion($bytes) !== 1;
        if (! $girada && max($medidas) <= self::LADO_MAXIMO) {
            return null;
        }

        $imagen = Imagen::abrir($bytes);
        if ($imagen === null) {
            return null;
        }
        $imagen = Imagen::reducir($imagen, self::LADO_MAXIMO);

        ob_start();
        $ok = match ($mime) {
            'image/png' => $this->png($imagen),
            'image/webp' => function_exists('imagewebp') && imagewebp($imagen, null, self::CALIDAD),
            default => imagejpeg($imagen, null, self::CALIDAD),
        };
        $contenido = (string) ob_get_clean();
        imagedestroy($imagen);

        return $ok && $contenido !== '' ? [$contenido, $formatos[$mime]] : null;
    }

    private function png(\GdImage $imagen): bool
    {
        imagesavealpha($imagen, true);

        return imagepng($imagen, null, 6);
    }
}
