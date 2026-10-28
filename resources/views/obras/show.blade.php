@extends('layouts.app')
@section('title', "{$obra->name} · Nando's Art")
@section('description', Str::limit($obra->description, 155))
@section('og_type', 'article')
@section('og_image', $obra->imagenUrl())

@section('content')
@php
    // La principal primero y después las adicionales, en el orden que fija el admin
    $imagenes = collect([$obra])->concat($obra->imagenes);
@endphp
<article class="max-w-6xl mx-auto py-10 px-4">
    <a href="{{ route('galeria') }}" class="text-blue-600 hover:underline">← Volver a la galería</a>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
        <div>
            {{-- Al pulsar la imagen se abre a pantalla completa (resources/js/lightbox.js) --}}
            <button type="button" data-lightbox-abrir="visor-obra" data-lightbox-indice="0" class="block w-full cursor-zoom-in" aria-label="Ampliar la imagen de {{ $obra->name }}">
                <img src="{{ $obra->imagenUrl() }}" @if ($obra->srcset()) srcset="{{ $obra->srcset() }}" sizes="(min-width: 1152px) 544px, (min-width: 1024px) 46vw, 92vw" @endif
                     alt="{{ $obra->name }}" class="w-full max-h-[80vh] object-contain rounded-xl shadow-lg bg-white">
            </button>

            {{-- Las demás imágenes de la obra: cada miniatura abre el visor en la suya --}}
            @if ($imagenes->count() > 1)
                <ul class="mt-4 grid grid-cols-4 sm:grid-cols-6 gap-2" aria-label="Más imágenes de {{ $obra->name }}">
                    @foreach ($imagenes as $imagen)
                        <li>
                            <button type="button" data-lightbox-abrir="visor-obra" data-lightbox-indice="{{ $loop->index }}" class="block w-full cursor-zoom-in" aria-label="Ver la imagen {{ $loop->iteration }} de {{ $imagenes->count() }}">
                                <img src="{{ $imagen->miniaturaUrl() }}" alt="" loading="lazy" class="w-full aspect-square object-cover rounded-md shadow-sm hover:shadow-md transition">
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-2">{{ $obra->name }}</h1>
            <p class="mb-6">
                @if ($obra->available)
                    <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">Disponible</span>
                @else
                    <span class="inline-block bg-gray-200 text-gray-700 text-sm font-semibold px-3 py-1 rounded-full">Vendida</span>
                @endif
            </p>

            <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-line mb-6">{{ $obra->description }}</p>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-gray-700 mb-8">
                <dt class="font-semibold">Técnica</dt>
                <dd>{{ $obra->style->label() }}</dd>
                <dt class="font-semibold">Tema</dt>
                <dd>{{ $obra->category->label() }}</dd>
                <dt class="font-semibold">Medidas</dt>
                <dd>{{ $obra->height }} x {{ $obra->width }} cm</dd>
                @if ($obra->year)
                    <dt class="font-semibold">Año</dt>
                    <dd>{{ $obra->year }}</dd>
                @endif
                <dt class="font-semibold">Precio</dt>
                <dd>{{ $obra->precioTexto() }}</dd>
            </dl>

            {{-- El formulario de presupuesto sale ya con el estilo, el tema y el nombre de esta obra --}}
            <a href="{{ route('encargos.create', ['obra' => $obra->slug]) }}"
               class="inline-block bg-linear-to-r from-blue-200 to-pink-200 text-gray-800 px-5 py-3 rounded-lg shadow-sm font-semibold hover:scale-105 transition">
                {{ $obra->available && $obra->price === null ? 'Consultar precio' : 'Pedir una obra parecida' }}
            </a>
        </div>
    </div>

    @if ($relacionadas->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Más obras en {{ $obra->style->label() }}</h2>
            <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($relacionadas as $otra)
                    <li>
                        <a href="{{ route('obras.show', $otra->slug) }}" class="block group">
                            <img src="{{ $otra->imagenUrl() }}" @if ($otra->srcset()) srcset="{{ $otra->srcset() }}" sizes="(min-width: 768px) 25vw, 50vw" @endif alt="{{ $otra->name }}" loading="lazy" class="w-full aspect-square object-cover rounded-lg shadow-sm group-hover:shadow-lg transition">
                            <span class="block mt-2 text-gray-800 font-medium group-hover:underline">{{ $otra->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</article>

<dialog id="visor-obra" class="m-auto max-w-none max-h-none w-screen h-screen bg-black/90 p-4 backdrop:bg-black/80" aria-label="{{ $obra->name }} a pantalla completa">
    <button type="button" data-lightbox-cerrar class="absolute top-4 right-4 z-10 text-white text-3xl leading-none px-3 py-1 rounded-sm hover:bg-white/10" aria-label="Cerrar">×</button>
    @foreach ($imagenes as $imagen)
        <img data-lightbox-imagen src="{{ $imagen->imagenUrl() }}" alt="{{ $obra->name }}{{ $imagenes->count() > 1 ? " ({$loop->iteration} de {$imagenes->count()})" : '' }}"
             @if (! $loop->first) hidden loading="lazy" @endif class="w-full h-full object-contain">
    @endforeach
    @if ($imagenes->count() > 1)
        <button type="button" data-lightbox-anterior class="absolute left-2 top-1/2 -translate-y-1/2 text-white text-4xl leading-none px-3 py-2 rounded-sm hover:bg-white/10" aria-label="Imagen anterior">‹</button>
        <button type="button" data-lightbox-siguiente class="absolute right-2 top-1/2 -translate-y-1/2 text-white text-4xl leading-none px-3 py-2 rounded-sm hover:bg-white/10" aria-label="Imagen siguiente">›</button>
        <p data-lightbox-contador class="absolute bottom-4 inset-x-0 text-center text-white/80" aria-live="polite">1 / {{ $imagenes->count() }}</p>
    @endif
</dialog>
<x-footer />
@endsection
