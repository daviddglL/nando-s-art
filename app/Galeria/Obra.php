<?php

namespace App\Galeria;

use App\Enums\Category;
use App\Enums\Style;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Una obra de la galería, tal como está escrita en resources/contenido/obras.php.
 * Los nombres de las propiedades son los mismos que tenía antes el modelo (name, style...),
 * así las vistas y la API que lee la galería no cambian.
 */
final class Obra
{
    use TieneImagen;

    /**
     * @param  Collection<int, Foto>  $imagenes
     */
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $name,
        public readonly string $description,
        public readonly Style $style,
        public readonly Category $category,
        public readonly float $height,
        public readonly float $width,
        public readonly string $imagen,
        public readonly ?int $year = null,
        public readonly ?float $price = null,
        public readonly bool $available = true,
        public readonly bool $featured = false,
        public readonly Collection $imagenes = new Collection,
    ) {}

    /**
     * Crea la obra a partir de un bloque del fichero de contenido. Si falta algo o está mal escrito,
     * lanza una excepción que dice qué obra y qué campo hay que corregir.
     *
     * @param  array<string, mixed>  $datos
     */
    public static function desdeArray(int $id, array $datos): self
    {
        $nombre = $datos['titulo'] ?? $datos['slug'] ?? "número {$id}";
        $falta = fn (string $campo) => new InvalidArgumentException("A la obra «{$nombre}» le falta el campo «{$campo}» en resources/contenido/obras.php");

        foreach (['slug', 'titulo', 'descripcion', 'tecnica', 'tema', 'alto', 'ancho', 'imagen'] as $campo) {
            if (! isset($datos[$campo]) || $datos[$campo] === '') {
                throw $falta($campo);
            }
        }

        $style = Style::tryFrom((string) $datos['tecnica'])
            ?? throw new InvalidArgumentException("La técnica «{$datos['tecnica']}» de la obra «{$nombre}» no existe. Valen: ".implode(', ', Style::values()));
        $category = Category::tryFrom((string) $datos['tema'])
            ?? throw new InvalidArgumentException("El tema «{$datos['tema']}» de la obra «{$nombre}» no existe. Valen: ".implode(', ', Category::values()));

        return new self(
            id: $id,
            slug: (string) $datos['slug'],
            name: (string) $datos['titulo'],
            description: (string) $datos['descripcion'],
            style: $style,
            category: $category,
            height: (float) $datos['alto'],
            width: (float) $datos['ancho'],
            imagen: (string) $datos['imagen'],
            year: isset($datos['año']) ? (int) $datos['año'] : null,
            price: isset($datos['precio']) ? (float) $datos['precio'] : null,
            available: ! ($datos['vendida'] ?? false),
            featured: (bool) ($datos['destacada'] ?? false),
            imagenes: collect($datos['imagenes'] ?? [])->map(fn ($imagen) => new Foto((string) $imagen))->values(),
        );
    }

    public function url(): string
    {
        return route('obras.show', $this->slug);
    }

    public function precioTexto(): string
    {
        return $this->price === null
            ? 'Precio a consultar'
            : number_format($this->price, 2, ',', '.').' €';
    }

    // Fecha del fichero de la foto: el sitemap la usa como «última modificación»
    public function modificada(): ?int
    {
        return is_file($this->rutaImagen()) ? (int) filemtime($this->rutaImagen()) : null;
    }

    /**
     * Forma estable de la obra para la galería (la que lee resources/js/galeria).
     *
     * @return array<string, mixed>
     */
    public function paraApi(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'year' => $this->year,
            'price' => $this->price,
            'precio_texto' => $this->precioTexto(),
            'available' => $this->available,
            'featured' => $this->featured,
            'height' => $this->height,
            'width' => $this->width,
            'style' => $this->style->value,
            'style_label' => $this->style->label(),
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'imagen_url' => $this->imagenUrl(),
            'srcset' => $this->srcset(),
            'imagenes' => $this->imagenes->map(fn (Foto $foto) => [
                'imagen_url' => $foto->imagenUrl(),
                'srcset' => $foto->srcset(),
            ])->all(),
            'url' => $this->url(),
        ];
    }
}
