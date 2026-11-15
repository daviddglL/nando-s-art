<?php

namespace App\Galeria;

use App\Enums\Category;
use App\Enums\Style;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Las obras de la galería, leídas de resources/contenido/obras.php (sin base de datos).
 * Se registra como singleton: el fichero se lee una vez por petición.
 */
class Catalogo
{
    // Obras destacadas que caben en la portada
    public const MAX_DESTACADAS = 6;

    /** @var Collection<int, Obra>|null */
    private ?Collection $obras = null;

    public function __construct(private readonly string $fichero) {}

    /**
     * Todas las obras, en el orden del fichero.
     *
     * @return Collection<int, Obra>
     */
    public function todas(): Collection
    {
        if ($this->obras !== null) {
            return $this->obras;
        }

        $datos = require $this->fichero;
        if (! is_array($datos)) {
            throw new InvalidArgumentException("{$this->fichero} tiene que devolver una lista de obras");
        }

        $obras = collect(array_values($datos))->map(fn (array $obra, int $i) => Obra::desdeArray($i + 1, $obra));

        $repetidos = $obras->countBy('slug')->filter(fn (int $veces) => $veces > 1)->keys();
        if ($repetidos->isNotEmpty()) {
            throw new InvalidArgumentException('Hay obras con el mismo slug: '.$repetidos->implode(', '));
        }

        return $this->obras = $obras;
    }

    public function buscar(string $slug): ?Obra
    {
        return $this->todas()->firstWhere('slug', $slug);
    }

    /**
     * Las que tienen 'destacada' => true, como mucho MAX_DESTACADAS (salen en la portada).
     *
     * @return Collection<int, Obra>
     */
    public function destacadas(): Collection
    {
        return $this->todas()->where('featured', true)->take(self::MAX_DESTACADAS)->values();
    }

    /**
     * Otras obras de la misma técnica, para seguir navegando desde una ficha.
     *
     * @return Collection<int, Obra>
     */
    public function relacionadas(Obra $obra, int $cuantas = 4): Collection
    {
        return $this->todas()
            ->filter(fn (Obra $otra) => $otra->style === $obra->style && $otra->slug !== $obra->slug)
            ->take($cuantas)
            ->values();
    }

    /**
     * Filtros de la galería: técnica, tema y texto (en el título o la descripción, sin distinguir mayúsculas ni tildes).
     *
     * @return Collection<int, Obra>
     */
    public function filtrar(?Style $style = null, ?Category $category = null, string $busqueda = ''): Collection
    {
        $texto = Str::lower(Str::ascii(trim($busqueda)));

        return $this->todas()
            ->when($style, fn (Collection $obras) => $obras->where('style', $style))
            ->when($category, fn (Collection $obras) => $obras->where('category', $category))
            ->when($texto !== '', fn (Collection $obras) => $obras->filter(
                fn (Obra $obra) => Str::contains(Str::lower(Str::ascii($obra->name.' '.$obra->description)), $texto),
            ))
            ->values();
    }
}
