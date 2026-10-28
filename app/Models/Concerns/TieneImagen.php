<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Imagen en storage/app/public/obras con sus miniaturas WebP (App\Actions\GenerarMiniaturas).
 * La usan la imagen principal de cada obra (Product) y sus imágenes adicionales (ObraImagen).
 *
 * @property string|null $imagen
 */
trait TieneImagen
{
    public function imagenUrl(): string
    {
        return asset('storage/obras/'.rawurlencode((string) $this->imagen));
    }

    // Las miniaturas WebP de cada imagen van en su propia carpeta
    public function carpetaMiniaturas(): string
    {
        return 'obras/miniaturas/'.pathinfo((string) $this->imagen, PATHINFO_FILENAME);
    }

    /**
     * Anchos de las miniaturas que existen, de menor a mayor.
     *
     * @return list<int>
     */
    public function anchosMiniaturas(): array
    {
        if (! $this->imagen) {
            return [];
        }

        $anchos = array_map(
            fn (string $fichero) => (int) pathinfo($fichero, PATHINFO_FILENAME),
            Storage::disk('public')->files($this->carpetaMiniaturas()),
        );
        sort($anchos);

        return array_values(array_filter($anchos));
    }

    // Valor del atributo srcset ("…/400.webp 400w, …/800.webp 800w"); null si no hay miniaturas
    public function srcset(): ?string
    {
        $carpeta = asset('storage/'.$this->carpetaMiniaturas());
        $srcset = array_map(fn (int $ancho) => "{$carpeta}/{$ancho}.webp {$ancho}w", $this->anchosMiniaturas());

        return $srcset ? implode(', ', $srcset) : null;
    }

    // La miniatura más pequeña (para listados); si no hay, la imagen original
    public function miniaturaUrl(): string
    {
        $ancho = $this->anchosMiniaturas()[0] ?? null;

        return $ancho ? asset('storage/'.$this->carpetaMiniaturas()."/{$ancho}.webp") : $this->imagenUrl();
    }

    public function borrarMiniaturas(): void
    {
        if ($this->imagen) {
            Storage::disk('public')->deleteDirectory($this->carpetaMiniaturas());
        }
    }

    // Borra el fichero de la imagen y sus miniaturas
    public function borrarImagen(): void
    {
        if ($this->imagen) {
            Storage::disk('public')->delete('obras/'.$this->imagen);
            $this->borrarMiniaturas();
        }
    }
}
