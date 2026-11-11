@extends('layouts.app')
@section('title', "{$pagina->titulo} · Nando's Art")
@section('description', $pagina->resumen())
@if ($pagina->fotoUrl())
    @section('og_image', $pagina->fotoUrl())
@endif

@push('head')
    @php
        $personaJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Fernando Delgado',
        'alternateName' => "Nando's Art",
        'description' => $pagina->resumen(),
        'image' => $pagina->fotoUrl() ?? asset('images/nandofoto-352.webp'),
        'url' => url('/'),
    ];
    @endphp
    <script type="application/ld+json">@json($personaJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)</script>
@endpush

@section('content')
<article class="contenedor py-16 md:py-24">
    <div class="grid grid-cols-1 md:grid-cols-[minmax(0,22rem)_1fr] gap-12 md:gap-20 items-start">
        <figure class="md:sticky md:top-28">
            <div class="marco rotate-[-1.5deg]">
                <img src="{{ $pagina->fotoUrl() ?? asset('images/nandofoto-352.webp') }}" alt="Fernando Delgado, el artista"
                     width="352" height="440" class="w-full aspect-[4/5] object-cover">
            </div>
            <figcaption class="mt-5 text-sm text-tinta-tenue text-center">Fernando Delgado · Nando's Art</figcaption>
        </figure>

        <div>
            <p class="antetitulo">El artista</p>
            <h1 class="titulo-pagina mt-3">{{ $pagina->titulo }}</h1>
            {{-- Texto plano: los saltos de línea del panel se respetan y no se interpreta HTML --}}
            <div class="mt-10 texto-largo whitespace-pre-line first-letter:float-left first-letter:font-display first-letter:text-7xl first-letter:leading-[0.8] first-letter:mr-3 first-letter:mt-1 first-letter:text-acento">{{ $pagina->contenido }}</div>

            <div class="mt-12 flex flex-wrap gap-3">
                <a href="{{ route('galeria') }}" class="btn btn-primario">Ver la galería</a>
                <a href="{{ route('paginas.contacto') }}" class="btn btn-secundario">Contactar</a>
            </div>
        </div>
    </div>
</article>
@endsection
