@extends('layouts.app')
@section('title', "{$obra->name} · Nando's Art")
@section('description', Str::limit($obra->description, 155))
@section('og_type', 'article')
@section('og_image', $obra->imagenUrl())

@section('content')
<article class="max-w-6xl mx-auto py-10 px-4">
    <a href="{{ route('galeria') }}" class="text-blue-600 hover:underline">← Volver a la galería</a>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
        {{-- Al pulsar la imagen se abre a pantalla completa (resources/js/lightbox.js) --}}
        <button type="button" data-lightbox-abrir="visor-obra" class="block w-full cursor-zoom-in" aria-label="Ampliar la imagen de {{ $obra->name }}">
            <img src="{{ $obra->imagenUrl() }}" alt="{{ $obra->name }}" class="w-full max-h-[80vh] object-contain rounded-xl shadow-lg bg-white">
        </button>

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

            <a href="{{ route('encargos.create') }}"
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
                            <img src="{{ $otra->imagenUrl() }}" alt="{{ $otra->name }}" loading="lazy" class="w-full aspect-square object-cover rounded-lg shadow-sm group-hover:shadow-lg transition">
                            <span class="block mt-2 text-gray-800 font-medium group-hover:underline">{{ $otra->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</article>

<dialog id="visor-obra" class="m-auto max-w-none max-h-none w-screen h-screen bg-black/90 p-4 backdrop:bg-black/80" aria-label="{{ $obra->name }} a pantalla completa">
    <button type="button" data-lightbox-cerrar class="absolute top-4 right-4 text-white text-3xl leading-none px-3 py-1 rounded-sm hover:bg-white/10" aria-label="Cerrar">×</button>
    <img src="{{ $obra->imagenUrl() }}" alt="{{ $obra->name }}" class="w-full h-full object-contain">
</dialog>
<x-footer />
@endsection
