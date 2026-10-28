<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// Página de texto que el artista edita desde el panel ("Sobre mí" y "Contacto")
class Pagina extends Model
{
    public const SOBRE_MI = 'sobre-mi';

    public const CONTACTO = 'contacto';

    // Contenido con el que nace cada página hasta que el artista lo cambie
    public const POR_DEFECTO = [
        self::SOBRE_MI => [
            'titulo' => 'Sobre mí',
            'contenido' => "Soy Fernando Delgado, un apasionado del arte y el diseño con años de experiencia en distintas técnicas y estilos.\n\nDesde acuarelas suaves hasta trazos marcados con rotulador, cada obra cuenta una historia distinta. En este sitio comparto mi trabajo, mi evolución y mis ideas.",
        ],
        self::CONTACTO => [
            'titulo' => 'Contacto',
            'contenido' => "¿Quieres una obra, un encargo personalizado o simplemente saludar? Escríbeme y te respondo lo antes posible.\n\nSi ya tienes una idea concreta, pide un presupuesto con los detalles y una imagen de referencia.",
            'instagram' => 'https://www.instagram.com/nandomtgalters/',
        ],
    ];

    protected $fillable = [
        'slug',
        'titulo',
        'contenido',
        'foto',
        'email',
        'telefono',
        'instagram',
        'ubicacion',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // En las rutas {pagina} solo valen las páginas conocidas; si aún no existe la fila, se crea
    public function resolveRouteBinding($value, $field = null): ?self
    {
        return is_string($value) && isset(self::POR_DEFECTO[$value]) ? static::obtener($value) : null;
    }

    // Devuelve la página guardada o la crea con su contenido por defecto
    public static function obtener(string $slug): self
    {
        return static::firstOrCreate(['slug' => $slug], self::POR_DEFECTO[$slug]);
    }

    /**
     * Todas las páginas editables, en el orden del menú.
     *
     * @return list<self>
     */
    public static function todas(): array
    {
        return array_map(static::obtener(...), array_keys(self::POR_DEFECTO));
    }

    public function esContacto(): bool
    {
        return $this->slug === self::CONTACTO;
    }

    public function url(): string
    {
        return route('paginas.'.$this->slug);
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/paginas/'.$this->foto) : null;
    }

    // Para la meta description: el primer trozo del texto, sin saltos de línea
    public function resumen(): string
    {
        return Str::limit((string) Str::of($this->contenido)->squish(), 155);
    }

    // Enlace tel: solo con los dígitos y el + inicial
    public function telefonoEnlace(): ?string
    {
        return $this->telefono ? 'tel:'.preg_replace('/[^\d+]/', '', $this->telefono) : null;
    }
}
