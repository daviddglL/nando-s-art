<?php

namespace App\Console\Commands;

use App\Galeria\Catalogo;
use App\Galeria\Foto;
use App\Galeria\Obra;
use App\Support\Imagen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

// Crea las versiones WebP pequeñas de las fotos de public/obras para que la web cargue rápido en el móvil
class GenerarMiniaturasObras extends Command
{
    // Anchos que se generan; las fotos más estrechas solo se convierten a WebP con su ancho real
    public const ANCHOS = [400, 800, 1600];

    protected $signature = 'obras:miniaturas';

    protected $description = 'Genera (o vuelve a generar) las miniaturas WebP de las fotos de todas las obras';

    public function handle(Catalogo $catalogo): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP no tiene la extensión GD con soporte WebP: no se pueden generar miniaturas.');

            return self::FAILURE;
        }

        foreach ($catalogo->todas() as $obra) {
            foreach (collect([$obra])->concat($obra->imagenes) as $foto) {
                $anchos = $this->generar($foto);
                $anchos
                    ? $this->line("{$obra->name} ({$foto->imagen}): ".implode(', ', $anchos).' px')
                    : $this->warn("{$obra->name}: no se puede abrir public/obras/{$foto->imagen}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return list<int> anchos generados (vacío si la foto no existe o no se puede abrir)
     */
    public function generar(Obra|Foto $foto): array
    {
        if (! is_file($foto->rutaImagen())) {
            return [];
        }

        // Girada según su EXIF, como la ve el navegador (las fotos de móvil en vertical salían tumbadas)
        $original = Imagen::abrir((string) file_get_contents($foto->rutaImagen()));
        if ($original === null) {
            return [];
        }

        $carpeta = public_path($foto->carpetaMiniaturas());
        File::deleteDirectory($carpeta);
        File::ensureDirectoryExists($carpeta);
        $foto->olvidarMiniaturas();

        $anchoOriginal = imagesx($original);
        $anchos = array_values(array_unique(array_map(fn (int $ancho) => min($ancho, $anchoOriginal), self::ANCHOS)));

        foreach ($anchos as $ancho) {
            $miniatura = imagescale($original, $ancho);
            if ($miniatura === false) {
                continue;
            }

            imagewebp($miniatura, "{$carpeta}/{$ancho}.webp", 80);
            imagedestroy($miniatura);
        }

        imagedestroy($original);

        return $anchos;
    }
}
