<?php

namespace App\Console\Commands;

use App\Actions\GenerarMiniaturas;
use App\Models\ObraImagen;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GenerarMiniaturasObras extends Command
{
    protected $signature = 'obras:miniaturas';

    protected $description = 'Genera (o vuelve a generar) las miniaturas WebP de todas las obras y sus imágenes adicionales';

    public function handle(GenerarMiniaturas $miniaturas): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('PHP no tiene la extensión GD con soporte WebP: no se pueden generar miniaturas.');

            return self::FAILURE;
        }

        Product::withTrashed()->each(function (Product $obra) use ($miniaturas) {
            $anchos = $miniaturas->handle($obra);
            $anchos
                ? $this->line("{$obra->name}: ".implode(', ', $anchos).' px')
                : $this->warn("{$obra->name}: no se encuentra la imagen {$obra->imagen}");
        });

        ObraImagen::with('obra')->each(function (ObraImagen $imagen) use ($miniaturas) {
            $nombre = "{$imagen->obra->name} (imagen adicional {$imagen->imagen})";
            $anchos = $miniaturas->handle($imagen);
            $anchos
                ? $this->line("{$nombre}: ".implode(', ', $anchos).' px')
                : $this->warn("{$nombre}: no se encuentra el fichero");
        });

        // La API guarda en caché las URLs de las imágenes: se renueva para que salgan las miniaturas
        Cache::forever(Product::VERSION_CACHE, (string) Str::uuid());

        return self::SUCCESS;
    }
}
