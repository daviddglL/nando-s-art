<?php

namespace App\Galeria;

use Illuminate\Support\Str;
use InvalidArgumentException;

// Página de texto «Sobre mí» o «Contacto», leída de resources/contenido/paginas.php
final class Pagina
{
    public const SOBRE_MI = 'sobre-mi';

    public const CONTACTO = 'contacto';

    public const FICHERO = 'contenido/paginas.php';

    public function __construct(
        public readonly string $slug,
        public readonly string $titulo,
        public readonly string $contenido,
        public readonly ?string $foto = null,
        public readonly ?string $email = null,
        public readonly ?string $telefono = null,
        public readonly ?string $instagram = null,
        public readonly ?string $ubicacion = null,
    ) {}

    public static function leer(string $slug): self
    {
        /** @var array<string, array<string, string|null>> $paginas */
        $paginas = once(fn () => require resource_path(self::FICHERO));
        $datos = $paginas[$slug] ?? throw new InvalidArgumentException("La página «{$slug}» no está en resources/".self::FICHERO);

        return new self(
            slug: $slug,
            titulo: (string) ($datos['titulo'] ?? Str::headline($slug)),
            contenido: (string) ($datos['contenido'] ?? ''),
            foto: ($datos['foto'] ?? null) ?: null,
            email: ($datos['email'] ?? null) ?: null,
            telefono: ($datos['telefono'] ?? null) ?: null,
            instagram: ($datos['instagram'] ?? null) ?: null,
            ubicacion: ($datos['ubicacion'] ?? null) ?: null,
        );
    }

    public function url(): string
    {
        return route('paginas.'.$this->slug);
    }

    // La foto va en public/images/
    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('images/'.rawurlencode($this->foto)) : null;
    }

    // Para la meta description: el primer trozo del texto, sin saltos de línea
    public function resumen(): string
    {
        return Str::limit((string) Str::of($this->contenido)->squish(), 155);
    }

    // El primer párrafo (sale en la portada)
    public function primerParrafo(int $limite = 420): string
    {
        return Str::limit(Str::before(trim($this->contenido)."\n\n", "\n\n"), $limite);
    }

    // Enlace tel: solo con los dígitos y el + inicial
    public function telefonoEnlace(): ?string
    {
        return $this->telefono ? 'tel:'.preg_replace('/[^\d+]/', '', $this->telefono) : null;
    }

    // «@usuario» a partir del enlace de Instagram
    public function usuarioInstagram(): ?string
    {
        return $this->instagram ? '@'.trim(Str::after($this->instagram, 'instagram.com/'), '/') : null;
    }
}
