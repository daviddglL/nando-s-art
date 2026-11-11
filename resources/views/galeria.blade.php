@extends('layouts.app')
@use('App\Enums\Category')
@use('App\Enums\Style')
@section('title', "Galería de obras · Nando's Art")
@section('description', 'Galería de Nando\'s Art: obras originales en acuarela, óleo, rotulador y más, ordenadas por estilo. Entra en cada una para ver la ficha y el precio.')

@section('content')
{{-- <section> y no <main>: el <main> ya lo pone el layout. El fondo cambia con el estilo (resources/js/galeria) --}}
<section id="galeria-main" aria-labelledby="titulo-galeria" class="transition-colors duration-700 {{ $estilo?->background() ?? 'bg-papel' }}">
    <div class="contenedor pt-14 pb-6">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="antetitulo">Obra original</p>
                <h1 id="titulo-galeria" class="titulo-pagina mt-3">Galería</h1>
            </div>
            @if(Auth::user()?->isAdmin())
                <a href="{{ route('products.create') }}" class="btn btn-primario btn-pequeno">+ Añadir nueva obra</a>
            @endif
        </div>

        <!-- Estilos -->
        <div class="mt-10 flex flex-wrap gap-2" role="group" aria-label="Filtrar por estilo">
            <button type="button" class="filtro-estilo btn btn-pequeno" aria-pressed="{{ $estilo ? 'false' : 'true' }}" data-style="">Todas</button>
            @foreach(Style::cases() as $style)
                <button
                    type="button"
                    class="filtro-estilo btn btn-pequeno"
                    aria-pressed="{{ $estilo === $style ? 'true' : 'false' }}"
                    data-style="{{ $style->value }}"
                >
                    {{ $style->label() }}
                </button>
            @endforeach
        </div>

        <!-- Tema y búsqueda: sin JavaScript, el formulario recarga la página con los filtros en la URL -->
        <form method="GET" action="{{ route('galeria') }}" class="mt-5 flex flex-wrap items-end gap-3" role="search">
            <input type="hidden" name="estilo" value="{{ $estilo?->value }}">
            <div>
                <label for="filtro-categoria" class="etiqueta">Tema</label>
                <select id="filtro-categoria" name="categoria" class="campo py-2 pr-10">
                    <option value="">Todos</option>
                    @foreach(Category::cases() as $category)
                        <option value="{{ $category->value }}" @selected($categoria === $category)>{{ $category->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grow sm:grow-0 sm:w-72">
                <label for="filtro-busqueda" class="etiqueta">Buscar</label>
                <input id="filtro-busqueda" type="search" name="q" value="{{ $busqueda }}" maxlength="100"
                       placeholder="Nombre o descripción" class="campo py-2">
            </div>
        </form>
    </div>

    <!-- Carrusel: lo pinta resources/js/galeria (MVI) -->
    <div class="relative contenedor pb-20">
        <div id="contenedor-muestras" class="flex justify-center items-center gap-5 min-h-[600px] py-8 overflow-hidden" aria-live="polite" aria-busy="true">
            <p class="w-full text-center text-tinta-tenue py-12">Cargando obras…</p>
        </div>

        <!-- Flechas -->
        <button id="prev" type="button" aria-label="Obra anterior" class="absolute left-2 sm:left-4 top-[calc(50%-2.5rem)] z-30 grid place-items-center w-12 h-12 rounded-full bg-papel-claro/90 border border-linea shadow-cuadro text-xl hover:bg-tinta hover:text-papel-claro transition disabled:opacity-30 disabled:pointer-events-none">
            <span aria-hidden="true">←</span>
        </button>
        <button id="next" type="button" aria-label="Obra siguiente" class="absolute right-2 sm:right-4 top-[calc(50%-2.5rem)] z-30 grid place-items-center w-12 h-12 rounded-full bg-papel-claro/90 border border-linea shadow-cuadro text-xl hover:bg-tinta hover:text-papel-claro transition disabled:opacity-30 disabled:pointer-events-none">
            <span aria-hidden="true">→</span>
        </button>
        <p class="text-center text-xs text-tinta-tenue">Usa las flechas del teclado o desliza el dedo para pasar de obra.</p>
    </div>
</section>
@endsection

@section('scripts')
{{-- Datos que necesita la galería; los colores salen del enum App\Enums\Style ('' = todas las obras) --}}
@php
    $config = [
        'estiloInicial' => $estilo?->value ?? '',
        'categoriaInicial' => $categoria?->value ?? '',
        'busquedaInicial' => $busqueda,
        'fondos' => collect(Style::cases())->mapWithKeys(fn ($s) => [$s->value => $s->background()])->put('', 'bg-papel'),
        'coloresBoton' => collect(Style::cases())->mapWithKeys(fn ($s) => [$s->value => $s->buttonClasses()])->put('', 'bg-tinta text-white hover:bg-acento'),
    ];
@endphp
<script type="application/json" id="galeria-config">@json($config)</script>
@vite('resources/js/galeria/index.js')
@endsection
