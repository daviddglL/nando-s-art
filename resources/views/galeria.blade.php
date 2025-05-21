<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería</title>
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
                <li><a href="{{ route('galeria') }}" class="text-gray-700 hover:text-gray-900 font-semibold">Galería</a></li>
                <li><a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Contenido principal -->
<main class="flex flex-col items-center min-h-screen">
    <h2 class="text-3xl font-bold text-gray-800 pt-12 mb-4">Muestras</h2>

    <div class="relative w-[calc(100vw-200px)] h-[calc(100vh-300px)] flex justify-start ml-6">
        <!-- Tabs -->
        <div class="absolute -top-8 flex space-x-1">
            @foreach(['Acuarela', 'Rotulador', 'Óleo', 'Sketch', 'Papel', 'Lienzo'] as $index => $style)
                <button 
                    class="px-6 py-2 bg-gray-300 text-gray-800 font-semibold rounded-t-lg border border-gray-400 relative filtro-estilo {{ $index !== 0 ? '-ml-2' : '' }} hover:bg-gray-400 transition"
                    data-style="{{ strtolower($style) }}"
                >
                    {{ $style }}
                </button>
            @endforeach
        </div>

        <!-- Caja de resultados -->
        <div class="bg-white shadow-lg rounded-lg p-6 w-full h-full border border-gray-400 relative overflow-y-auto">
            <div id="contenedor-muestras" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 h-full"></div>
        </div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-estilo");
    const contenedor = document.getElementById("contenedor-muestras");

    const cargarEstilo = (estilo) => {
        fetch(`/api/products?style=${estilo}`)
            .then(response => response.json())
            .then(data => {
                contenedor.innerHTML = "";

                if (data.length === 0) {
                    contenedor.innerHTML = "<p class='text-gray-500'>No hay productos para este estilo.</p>";
                    return;
                }

                data.forEach(producto => {
                    const card = document.createElement("div");
                    card.className = "bg-white p-4 rounded-lg shadow-md border border-gray-200";

                    card.innerHTML = `
                        <h3 class="text-xl font-bold text-gray-800 mb-2">${producto.name}</h3>
                        <img src="/storage/${producto.imagen}" alt="${producto.name}" class="w-full h-48 object-cover rounded mb-2"/>
                        <p class="text-gray-600">${producto.description}</p>
                        <p class="text-sm text-gray-500 mt-1">Tamaño: ${producto.height} x ${producto.width}</p>
                        <p class="text-sm text-gray-500">Categoría: ${producto.category}</p>
                    `;
                    contenedor.appendChild(card);
                });
            });
    };

    // Carga inicial
    cargarEstilo("acuarela");

    botones.forEach(boton => {
        boton.addEventListener("click", () => {
            cargarEstilo(boton.dataset.style);
        });
    });
});
</script>

</body>
</html>
