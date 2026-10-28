@extends('layouts.app')
@section('title', "{$pagina->titulo} · Nando's Art")
@section('description', $pagina->resumen())
@if ($pagina->fotoUrl())
    @section('og_image', $pagina->fotoUrl())
@endif

@section('content')
<article class="bg-linear-to-br from-blue-100 via-purple-100 to-pink-100 py-16 px-4">
    <div class="max-w-4xl mx-auto bg-white/80 rounded-xl shadow-lg p-6 sm:p-10 grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
        <img src="{{ $pagina->fotoUrl() ?? asset('images/nandofoto-352.webp') }}" alt="Fernando Delgado, el artista"
             class="w-44 h-44 md:w-full md:h-auto mx-auto rounded-full md:rounded-xl object-cover shadow-xl border-4 border-white">

        <div class="md:col-span-2">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-6 tracking-tight">{{ $pagina->titulo }}</h1>
            {{-- Texto plano: los saltos de línea del panel se respetan y no se interpreta HTML --}}
            <div class="text-gray-700 text-lg leading-relaxed whitespace-pre-line">{{ $pagina->contenido }}</div>

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('galeria') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow-sm">Ver la galería</a>
                <a href="{{ route('paginas.contacto') }}" class="inline-block border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-bold py-2 px-5 rounded-lg">Contactar</a>
            </div>
        </div>
    </div>
</article>

<x-footer />
@endsection
