@extends('layouts.app')
@section('title', "Nando's Art · Acuarelas y retratos de Fernando Delgado")
@section('description', 'Acuarelas, retratos y paisajes de Fernando Delgado. Descubre sus obras originales y pide un encargo personalizado.')

@section('content')
{{-- El <main> ya lo pone el layout --}}
<div class="flex flex-col items-center justify-center min-h-screen bg-linear-to-br from-blue-100 via-purple-100 to-pink-100 py-16 px-4">
    <!-- Imagen redonda con borde animado -->
    <div class="relative mb-8">
        <div class="absolute inset-0 rounded-full bg-linear-to-tr from-blue-300 via-pink-200 to-purple-300 blur-lg opacity-60 animate-pulse"></div>
        <img src="{{ asset('images/nandofoto-352.webp') }}" alt="Fernando Delgado, el artista" width="176" height="176" class="w-44 h-44 rounded-full object-cover shadow-2xl border-4 border-white relative z-10">
    </div>

    <!-- Biografía -->
    <div class="max-w-3xl bg-white/70 backdrop-blur-md rounded-xl shadow-lg p-8 mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4 tracking-tight">¡Bienvenido a mi espacio creativo!</h1>
        <p class="text-gray-700 text-lg leading-relaxed">
            Soy un apasionado del arte y el diseño con años de experiencia en distintas técnicas y estilos. Desde acuarelas suaves hasta trazos marcados con rotulador, cada obra cuenta una historia distinta. En este sitio comparto mi trabajo, mi evolución y mis ideas. Gracias por acompañarme en este viaje visual, donde cada imagen busca inspirar, provocar y emocionar.
        </p>
    </div>

    <!-- Frase del día -->
    <div class="w-full max-w-xl bg-linear-to-r from-blue-200 via-purple-100 to-pink-200 rounded-lg shadow-sm p-6 mb-12">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Frase del Día</h2>
       <div id="frase-del-dia" class="mt-8 text-center px-4"></div>

    </div>

    {{-- Obras destacadas: las marca el admin en /admin/obras --}}
    @if ($destacadas->isNotEmpty())
        <section class="w-full max-w-5xl mb-16" aria-labelledby="titulo-destacadas">
            <h2 id="titulo-destacadas" class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 text-center">Obras destacadas</h2>
            <ul class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                @foreach ($destacadas as $obra)
                    <li>
                        <a href="{{ route('obras.show', $obra->slug) }}" class="block group bg-white/80 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                            <img src="{{ $obra->imagenUrl() }}" @if ($obra->srcset()) srcset="{{ $obra->srcset() }}" sizes="(min-width: 768px) 320px, 50vw" @endif alt="{{ $obra->name }}" loading="lazy" class="w-full aspect-square object-cover">
                            <span class="block px-3 pt-2 font-semibold text-gray-800 group-hover:underline">{{ $obra->name }}</span>
                            <span class="block px-3 pb-3 text-sm text-gray-600">{{ $obra->available ? $obra->precioTexto() : 'Vendida' }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <p class="text-center mt-6">
                <a href="{{ route('galeria') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm">Ver toda la galería</a>
            </p>
        </section>
    @endif

    <!-- Sección Zig-Zag -->
    <section class="mt-8 space-y-20 max-w-5xl w-full">
        <!-- Bloque 1 -->
        <div class="flex flex-col md:flex-row items-center md:space-x-10 bg-white/80 rounded-xl shadow-lg p-6 hover:scale-[1.02] transition-transform duration-300">
            <img src="{{ asset('images/mujer-960.webp') }}"
                 srcset="{{ asset('images/mujer-480.webp') }} 480w, {{ asset('images/mujer-960.webp') }} 960w"
                 sizes="(min-width: 1024px) 460px, (min-width: 768px) 45vw, 90vw"
                 alt="Retrato en acuarela de un hombre mayor de perfil, con turbante rojo y barba blanca sobre un fondo gris oscuro" loading="lazy" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
            <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
                Esta obra refleja la serenidad de los paisajes naturales, capturada con acuarela. Las capas sutiles de color dan vida a cada rincón del cuadro.
            </p>
        </div>

        <!-- Bloque 2 (invertido) -->
        <div class="flex flex-col md:flex-row-reverse items-center md:space-x-10 md:space-x-reverse bg-white/80 rounded-xl shadow-lg p-6 hover:scale-[1.02] transition-transform duration-300">
            <img src="{{ asset('images/zig2-960.webp') }}"
                 srcset="{{ asset('images/zig2-480.webp') }} 480w, {{ asset('images/zig2-960.webp') }} 960w"
                 sizes="(min-width: 1024px) 460px, (min-width: 768px) 45vw, 90vw"
                 alt="Retrato en acuarela de un hombre con gorra gris y barba canosa, mirando hacia la derecha" loading="lazy" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
            <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
                Una explosión de color y energía. Este estilo a rotulador crea líneas fuertes que exploran el dinamismo del movimiento urbano.
            </p>
        </div>

        <!-- Bloque 3 -->
        <div class="flex flex-col md:flex-row items-center md:space-x-10 bg-white/80 rounded-xl shadow-lg p-6 hover:scale-[1.02] transition-transform duration-300">
            <img src="{{ asset('images/zig3-960.webp') }}"
                 srcset="{{ asset('images/zig3-480.webp') }} 480w, {{ asset('images/zig3-960.webp') }} 960w"
                 sizes="(min-width: 1024px) 460px, (min-width: 768px) 45vw, 90vw"
                 alt="Paisaje en acuarela de unas casas blancas junto a un río en calma, con hierba alta y cielo gris" loading="lazy" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
            <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
                Con una técnica más abstracta, esta pieza en óleo juega con la textura y la intuición para transmitir emociones puras e instintivas.
            </p>
        </div>
    </section>

</div>

<x-footer />
@endsection
