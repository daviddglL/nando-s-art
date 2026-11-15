<?php

namespace App\Galeria;

/**
 * Foto en public/obras con sus miniaturas WebP en public/obras/miniaturas/{nombre}/{ancho}.webp
 * (las crea `php artisan obras:miniaturas`). Si no hay miniaturas se usa la foto tal cual.
 */
trait TieneImagen
{
    public const CARPETA = 'obras';

    /** @var list<int>|null anchos de miniaturas ya leídos del disco */
    private ?array $anchosLeidos = null;

    public function rutaImagen(): string
    {
        return public_path(self::CARPETA.'/'.$this->imagen);
    }

    public function imagenUrl(): string
    {
        return asset(self::CARPETA.'/'.rawurlencode($this->imagen));
    }

    // Las miniaturas de cada foto van en su propia carpeta
    public function carpetaMiniaturas(): string
    {
        return self::CARPETA.'/miniaturas/'.pathinfo($this->imagen, PATHINFO_FILENAME);
    }

    /**
     * Anchos de las miniaturas que existen, de menor a mayor.
     *
     * @return list<int>
     */
    public function anchosMiniaturas(): array
    {
        if ($this->anchosLeidos !== null) {
            return $this->anchosLeidos;
        }

        $anchos = array_map(
            fn (string $fichero) => (int) pathinfo($fichero, PATHINFO_FILENAME),
            glob(public_path($this->carpetaMiniaturas()).'/*.webp') ?: [],
        );
        sort($anchos);

        return $this->anchosLeidos = array_values(array_filter($anchos));
    }

    public function olvidarMiniaturas(): void
    {
        $this->anchosLeidos = null;
    }

    // Valor del atributo srcset ("…/400.webp 400w, …/800.webp 800w"); null si no hay miniaturas
    public function srcset(): ?string
    {
        $carpeta = asset($this->carpetaMiniaturas());
        $srcset = array_map(fn (int $ancho) => "{$carpeta}/{$ancho}.webp {$ancho}w", $this->anchosMiniaturas());

        return $srcset ? implode(', ', $srcset) : null;
    }

    // La miniatura más pequeña (para listados); si no hay, la foto original
    public function miniaturaUrl(): string
    {
        $ancho = $this->anchosMiniaturas()[0] ?? null;

        return $ancho ? asset($this->carpetaMiniaturas()."/{$ancho}.webp") : $this->imagenUrl();
    }

    // La miniatura más grande (1600 px): para el visor a pantalla completa y al compartir en redes
    public function imagenGrandeUrl(): string
    {
        $anchos = $this->anchosMiniaturas();
        $ancho = end($anchos);

        return $ancho ? asset($this->carpetaMiniaturas()."/{$ancho}.webp") : $this->imagenUrl();
    }
}
