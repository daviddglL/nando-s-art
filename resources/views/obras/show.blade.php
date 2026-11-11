@extends('layouts.app')
@section('title', "{$obra->name} · Nando's Art")
@section('description', Str::limit($obra->description, 155))
@section('og_type', 'article')
@section('og_image', $obra->imagenGrandeUrl())

@php
    // La principal primero y después las adicionales, en el orden que fija el admin
    $imagenes = collect([$obra])->concat($obra->imagenes);

    // Datos estructurados: Google puede enseñar la obra con su imagen y, si está a la venta, su precio
    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'VisualArtwork',
        'name' => $obra->name,
        'description' => $obra->description,
        'image' => $imagenes->map(fn ($imagen) => $imagen->imagenGrandeUrl())->all(),
        'url' => route('obras.show', $obra->slug),
        'artform' => 'Pintura',
        'artMedium' => $obra->style->label(),
        'genre' => $obra->category->label(),
        'dateCreated' => $obra->year ? (string) $obra->year : null,
        'height' => ['@type' => 'Distance', 'name' => "{$obra->height} cm"],
        'width' => ['@type' => 'Distance', 'name' => "{$obra->width} cm"],
        'creator' => ['@type' => 'Person', 'name' => 'Fernando Delgado', 'url' => url('/')],
        'offers' => $obra->available && $obra->price !== null ? [
            '@type' => 'Offer',
            'price' => (string) $obra->price,
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
            'url' => route('obras.show', $obra->slug),
        ] : null,
    ]);
@endphp

@push('head')
    <script type="application/ld+json">@json($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)</script>
@endpush

@section('content')
<article class="contenedor pt-8 pb-10">
    <nav aria-label="Migas de pan" class="text-sm text-tinta-tenue">
        <a href="{{ route('galeria') }}" class="hover:text-tinta">← Volver a la galería</a>
        <span class="mx-2" aria-hidden="true">/</span>
        <a href="{{ route('galeria', ['estilo' => $obra->style->value]) }}" class="hover:text-tinta">{{ $obra->style->label() }}</a>
    </nav>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-[1.25fr_1fr] gap-10 lg:gap-16 items-start">
        <div class="lg:sticky lg:top-28">
            {{-- Al pulsar la imagen se abre a pantalla completa (resources/js/lightbox.js) --}}
            <button type="button" data-lightbox-abrir="visor-obra" data-lightbox-indice="0" class="group block w-full cursor-zoom-in marco" aria-label="Ampliar la imagen de {{ $obra->name }}">
                <img src="{{ $obra->imagenUrl() }}" @if ($obra->srcset()) srcset="{{ $obra->srcset() }}" sizes="(min-width: 1152px) 600px, (min-width: 1024px) 52vw, 92vw" @endif
                     alt="{{ $obra->name }}" class="w-full max-h-[78vh] object-contain bg-papel-claro">
            </button>

            {{-- Las demás imágenes de la obra: cada miniatura abre el visor en la suya --}}
            @if ($imagenes->count() > 1)
                <ul class="mt-4 grid grid-cols-5 sm:grid-cols-6 gap-2" aria-label="Más imágenes de {{ $obra->name }}">
                    @foreach ($imagenes as $imagen)
                        <li>
                            <button type="button" data-lightbox-abrir="visor-obra" data-lightbox-indice="{{ $loop->index }}" class="block w-full cursor-zoom-in" aria-label="Ver la imagen {{ $loop->iteration }} de {{ $imagenes->count() }}">
                                <img src="{{ $imagen->miniaturaUrl() }}" alt="" loading="lazy" class="w-full aspect-square object-cover ring-1 ring-linea hover:ring-2 hover:ring-acento transition">
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <p class="antetitulo">{{ $obra->style->label() }} · {{ $obra->category->label() }}</p>
            <h1 class="titulo-pagina mt-3">{{ $obra->name }}</h1>
            <p class="mt-5">
                @if ($obra->available)
                    <span class="chip chip-disponible"><span class="w-1.5 h-1.5 rounded-full bg-salvia" aria-hidden="true"></span>Disponible</span>
                @else
                    <span class="chip chip-vendida">Vendida</span>
                @endif
            </p>

            <p class="mt-8 texto-largo whitespace-pre-line">{{ $obra->description }}</p>

            {{-- Cartela con la ficha técnica --}}
            <dl class="mt-10 grid grid-cols-[auto_1fr] gap-x-8 border-t border-linea text-sm">
                <dt class="py-3 border-b border-linea text-tinta-tenue">Técnica</dt>
                <dd class="py-3 border-b border-linea">{{ $obra->style->label() }}</dd>
                <dt class="py-3 border-b border-linea text-tinta-tenue">Tema</dt>
                <dd class="py-3 border-b border-linea">{{ $obra->category->label() }}</dd>
                <dt class="py-3 border-b border-linea text-tinta-tenue">Medidas</dt>
                <dd class="py-3 border-b border-linea">{{ $obra->height }} x {{ $obra->width }} cm</dd>
                @if ($obra->year)
                    <dt class="py-3 border-b border-linea text-tinta-tenue">Año</dt>
                    <dd class="py-3 border-b border-linea">{{ $obra->year }}</dd>
                @endif
                <dt class="py-3 border-b border-linea text-tinta-tenue">Precio</dt>
                <dd class="py-3 border-b border-linea font-display text-xl">{{ $obra->precioTexto() }}</dd>
            </dl>

            {{-- El formulario de presupuesto sale ya con el estilo, el tema y el nombre de esta obra --}}
            <div class="mt-10 flex flex-wrap items-center gap-4">
                <a href="{{ route('encargos.create', ['obra' => $obra->slug]) }}" class="btn btn-primario">
                    {{ $obra->available && $obra->price === null ? 'Consultar precio' : 'Pedir una obra parecida' }}
                </a>
                <a href="{{ route('paginas.contacto') }}" class="enlace text-sm">¿Dudas? Escríbeme</a>
            </div>
        </div>
    </div>

    @if ($relacionadas->isNotEmpty())
        <section class="mt-24" aria-labelledby="titulo-relacionadas">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <h2 id="titulo-relacionadas" class="titulo-seccion">Más obras en {{ $obra->style->label() }}</h2>
                <a href="{{ route('galeria', ['estilo' => $obra->style->value]) }}" class="enlace text-sm">Ver todas →</a>
            </div>
            <ul class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                @foreach ($relacionadas as $otra)
                    <li>
                        <a href="{{ route('obras.show', $otra->slug) }}" class="group block">
                            <div class="marco transition duration-500 group-hover:-translate-y-1 group-hover:shadow-flotante">
                                <img src="{{ $otra->imagenUrl() }}" @if ($otra->srcset()) srcset="{{ $otra->srcset() }}" sizes="(min-width: 768px) 25vw, 50vw" @endif alt="{{ $otra->name }}" loading="lazy" class="w-full aspect-[4/5] object-cover">
                            </div>
                            <span class="block mt-3 font-display text-lg group-hover:text-acento transition">{{ $otra->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</article>

<dialog id="visor-obra" class="m-auto max-w-none max-h-none w-screen h-screen bg-noche/95 p-4 sm:p-10 backdrop:bg-noche/80" aria-label="{{ $obra->name }} a pantalla completa">
    <button type="button" data-lightbox-cerrar class="absolute top-4 right-4 z-10 grid place-items-center w-11 h-11 rounded-full text-white text-2xl hover:bg-white/10" aria-label="Cerrar">×</button>
    @foreach ($imagenes as $imagen)
        {{-- La de 1600 px y no la original (puede pesar varios MB); lazy: no se descarga hasta abrir el visor --}}
        <img data-lightbox-imagen src="{{ $imagen->imagenGrandeUrl() }}" alt="{{ $obra->name }}{{ $imagenes->count() > 1 ? " ({$loop->iteration} de {$imagenes->count()})" : '' }}"
             loading="lazy" @if (! $loop->first) hidden @endif class="w-full h-full object-contain">
    @endforeach
    @if ($imagenes->count() > 1)
        <button type="button" data-lightbox-anterior class="absolute left-3 top-1/2 -translate-y-1/2 grid place-items-center w-12 h-12 rounded-full text-white text-2xl hover:bg-white/10" aria-label="Imagen anterior">←</button>
        <button type="button" data-lightbox-siguiente class="absolute right-3 top-1/2 -translate-y-1/2 grid place-items-center w-12 h-12 rounded-full text-white text-2xl hover:bg-white/10" aria-label="Imagen siguiente">→</button>
        <p data-lightbox-contador class="absolute bottom-5 inset-x-0 text-center text-sm tracking-widest text-white/70" aria-live="polite">1 / {{ $imagenes->count() }}</p>
    @endif
</dialog>
@endsection
