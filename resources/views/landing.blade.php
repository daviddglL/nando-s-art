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
<main class="flex flex-col items-center min-h-screen">
    <!-- Título principal -->
    <!-- Título principal con padding superior -->
<h2 class="text-3xl font-bold text-gray-800 pt-12 mb-4">Muestras</h2>

    <div class="relative w-[calc(100vw-200px)] h-[calc(100vh-300px)] flex justify-start ml-6">
        <!-- Pestañas tipo archivador -->
        <div class="absolute -top-8 flex space-x-1">
            @foreach(['Acuarela', 'Rotulador', 'Óleo', 'Sketch', 'Papel', 'Lienzo'] as $index => $style)
                <button class="px-6 py-2 bg-gray-300 text-gray-800 font-semibold rounded-t-lg border border-gray-400 relative 
                {{ $index !== 0 ? '-ml-2' : '' }} hover:bg-gray-400 transition">
                    {{ $style }}
                </button>
            @endforeach
        </div>

        <!-- Caja principal -->
        <div class="bg-white shadow-lg rounded-lg p-6 w-full h-full border border-gray-400 relative">
        </div>
    </div>
</main>



</body>
</html>
