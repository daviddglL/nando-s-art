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
</main>




</body>
</html>
