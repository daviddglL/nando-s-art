<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muestras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-800">
                <a href="{{ url('/') }}">Mi Aplicación</a>
            </h1>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="{{ route('landing') }}" class="text-gray-700 hover:text-gray-900">Inicio</a></li>
                    <li><a href="{{ route('galeria') }}" class="text-blue-600 font-semibold hover:underline">Galería</a></li>
                    <li><a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Iniciar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenedor Principal -->
<!-- Contenedor Principal -->
<main class="flex flex-col items-center justify-center py-16 px-4 text-center">
    <!-- Imagen redonda -->
    <img src="{{ asset('images/nandofoto.jpg') }}" alt="Autor" class="w-40 h-40 rounded-full object-cover shadow-lg mb-6">

    <!-- Biografía -->
    <p class="max-w-3xl text-gray-700 text-lg leading-relaxed">
        ¡Bienvenido a mi espacio creativo! Soy un apasionado del arte y el diseño con años de experiencia en distintas técnicas y estilos. Desde acuarelas suaves hasta trazos marcados con rotulador, cada obra cuenta una historia distinta. En este sitio comparto mi trabajo, mi evolución y mis ideas. Gracias por acompañarme en este viaje visual, donde cada imagen busca inspirar, provocar y emocionar.
    </p>
    <!-- Contenedor para la frase del día -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800">Frase del Día</h2>
        <p id="frase" class="text-gray-600"></p>
<div id="frase-del-dia" class="mt-8 text-center px-4"></div>

<!-- Sección Zig-Zag -->
<section class="mt-16 space-y-16 max-w-5xl mx-auto">
    <!-- Bloque 1 -->
    <div class="flex flex-col md:flex-row items-center md:space-x-8">
        <img src="{{ asset('images/zig1.jpg') }}" alt="Obra 1" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
        <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
            Esta obra refleja la serenidad de los paisajes naturales, capturada con acuarela. Las capas sutiles de color dan vida a cada rincón del cuadro.
        </p>
    </div>

    <!-- Bloque 2 (invertido) -->
    <div class="flex flex-col md:flex-row-reverse items-center md:space-x-8 md:space-x-reverse">
        <img src="{{ asset('images/zig2.jpg') }}" alt="Obra 2" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
        <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
            Una explosión de color y energía. Este estilo a rotulador crea líneas fuertes que exploran el dinamismo del movimiento urbano.
        </p>
    </div>

    <!-- Bloque 3 -->
    <div class="flex flex-col md:flex-row items-center md:space-x-8">
        <img src="{{ asset('images/zig3.jpg') }}" alt="Obra 3" class="w-full md:w-1/2 rounded-lg shadow-lg mb-4 md:mb-0">
        <p class="text-gray-700 text-lg leading-relaxed md:w-1/2">
            Con una técnica más abstracta, esta pieza en óleo juega con la textura y la intuición para transmitir emociones puras e instintivas.
        </p>
    </div>
</section>
    </div>
    </div>  

<script src="{{ asset('js/frase.js') }}"></script>


</main>




</body>
</html>
