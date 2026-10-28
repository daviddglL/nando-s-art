@extends('layouts.app')
@use('App\Enums\Style')
@section('content')
<main id="galeria-main" class="flex flex-col items-center min-h-screen bg-gray-100 transition-colors duration-500 px-4">
    <h2 class="text-3xl font-bold text-gray-800 pt-12 mb-4">Muestras</h2>

    <!-- Estilos -->
    <div class="flex flex-wrap justify-center gap-2 mb-4" role="group" aria-label="Filtrar por estilo">
        @foreach(Style::cases() as $style)
            <button
                type="button"
                class="px-4 py-2 font-semibold rounded-sm filtro-estilo bg-gray-200 text-gray-800"
                aria-pressed="false"
                data-style="{{ $style->value }}"
            >
                {{ $style->label() }}
            </button>
        @endforeach
    </div>

    <!-- Carrusel: lo pinta resources/js/galeria (MVI) -->
    <div class="relative w-full max-w-6xl rounded-xl px-10 py-6 overflow-hidden">

        <div id="contenedor-muestras" class="flex justify-center items-center gap-4 min-h-[560px]" aria-live="polite" aria-busy="true">
            <p class="w-full text-center text-gray-600 py-12">Cargando obras…</p>
        </div>

        <!-- Flechas -->
        <button id="prev" type="button" aria-label="Obra anterior" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white shadow-sm p-2 rounded-full hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
            ◀
        </button>
        <button id="next" type="button" aria-label="Obra siguiente" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white shadow-sm p-2 rounded-full hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
            ▶
        </button>
    </div>

    @if(Auth::user()?->isAdmin())
        <a href="{{ route('products.create') }}"
           class="mt-6 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-sm">
           Añadir Nueva Obra
        </a>
    @endif
</main>
<x-footer />
@endsection

@section('scripts')
{{-- Datos que necesita la galería; los colores salen del enum App\Enums\Style --}}
@php
    $config = [
        'estiloInicial' => Style::Acuarela->value,
        'fondos' => collect(Style::cases())->mapWithKeys(fn ($s) => [$s->value => $s->background()]),
        'coloresBoton' => collect(Style::cases())->mapWithKeys(fn ($s) => [$s->value => $s->buttonClasses()]),
    ];
@endphp
<script type="application/json" id="galeria-config">@json($config)</script>
@vite('resources/js/galeria/index.js')
@endsection
