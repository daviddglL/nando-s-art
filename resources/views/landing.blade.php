@extends('layouts.app')
@section('content')
<main class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 py-16 px-4">
    <!-- Imagen redonda con borde animado -->
    <div class="relative mb-8">
        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-blue-300 via-pink-200 to-purple-300 blur-lg opacity-60 animate-pulse"></div>
        <img src="{{ asset('images/nandofoto.jpg') }}" alt="Autor" class="w-44 h-44 rounded-full object-cover shadow-2xl border-4 border-white relative z-10">
    </div>

    <!-- Biografía -->
    <div class="max-w-3xl bg-white/70 backdrop-blur-md rounded-xl shadow-lg p-8 mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4 tracking-tight">¡Bienvenido a mi espacio creativo!</h1>
        <p class="text-gray-700 text-lg leading-relaxed">
            Soy un apasionado del arte y el diseño con años de experiencia en distintas técnicas y estilos. Desde acuarelas suaves hasta trazos marcados con rotulador, cada obra cuenta una historia distinta. En este sitio comparto mi trabajo, mi evolución y mis ideas. Gracias por acompañarme en este viaje visual, donde cada imagen busca inspirar, provocar y emocionar.
        </p>
    </div>

    <!-- Frase del día -->
    <div class="w-full max-w-xl bg-gradient-to-r from-blue-200 via-purple-100 to-pink-200 rounded-lg shadow p-6 mb-12">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Frase del Día</h2>
       <div id="frase-del-dia" class="mt-8 text-center px-4"></div>

    </div>

    <!-- Sección Zig-Zag -->
    <section class="mt-8 space-y-20 max-w-5xl w-full">
        <!-- Bloque 1 -->
        <div class="flex flex-col md:flex-row items-center md:space-x-10 bg-white/80 rounded-xl shadow-lg p-6 hover:scale-[1.02] transition-transform duration-300">
            <img src="{{ asset('images/zig1.jpg') }}" alt="Obra 1" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
            <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
                Esta obra refleja la serenidad de los paisajes naturales, capturada con acuarela. Las capas sutiles de color dan vida a cada rincón del cuadro.
            </p>
        </div>

        <!-- Bloque 2 (invertido) -->
        <div class="flex flex-col md:flex-row-reverse items-center md:space-x-10 md:space-x-reverse bg-white/80 rounded-xl shadow-lg p-6 hover:scale-[1.02] transition-transform duration-300">
            <img src="{{ asset('images/zig2.jpeg') }}" alt="Obra 2" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
            <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
                Una explosión de color y energía. Este estilo a rotulador crea líneas fuertes que exploran el dinamismo del movimiento urbano.
            </p>
        </div>

        <!-- Bloque 3 -->
        <div class="flex flex-col md:flex-row items-center md:space-x-10 bg-white/80 rounded-xl shadow-lg p-6 hover:scale-[1.02] transition-transform duration-300">
            <img src="{{ asset('images/zig3.jpeg') }}" alt="Obra 3" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
            <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
                Con una técnica más abstracta, esta pieza en óleo juega con la textura y la intuición para transmitir emociones puras e instintivas.
            </p>
        </div>
    </section>

    <script src="{{ asset('js/frase.js') }}"></script>
</main>
@endsection
