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
                @guest
                    <li><a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Iniciar Sesión</a></li>
                    <li><a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline ml-4">Crear cuenta</a></li>
                @endguest

                @auth
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 font-semibold hover:underline">Cerrar sesión</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>
    </div>
</header>

<!-- Contenido principal -->
<main class="flex flex-col items-center min-h-screen">
    <h2 class="text-3xl font-bold text-gray-800 pt-12 mb-4">Muestras</h2>

    <!-- Estilos -->
    <div class="flex space-x-2 mb-4">
        @foreach(['Acuarela', 'Rotulador', 'Óleo', 'Sketch', 'Papel', 'Lienzo'] as $index => $style)
            <button 
                class="px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded hover:bg-gray-400 filtro-estilo"
                data-style="{{ strtolower($style) }}"
            >
                {{ $style }}
            </button>
        @endforeach
    </div>

    <!-- Carrusel -->
    <div class="relative w-[1150px] border-2 border-gray-300 bg-white rounded-xl shadow-md px-4 py-6 overflow-hidden">

        <div id="contenedor-muestras" class="flex transition-transform duration-300 ease-in-out space-x-4">
            <!-- Tarjetas dinámicas aquí -->
        </div>

        <!-- Flechas -->
        <button id="prev" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white shadow p-2 rounded-full hover:bg-gray-100">
            ◀
        </button>
        <button id="next" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white shadow p-2 rounded-full hover:bg-gray-100">
            ▶
        </button>
    </div>

    @if(Auth::check() && Auth::user()->role === 'admin')
        <a href="{{ route('products.create') }}"
           class="mt-6 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
           Añadir Nueva Obra
        </a>
    @endif
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-estilo");
    const contenedor = document.getElementById("contenedor-muestras");
    const btnNext = document.getElementById("next");
    const btnPrev = document.getElementById("prev");

    let currentIndex = 0;

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
                    console.log(producto);
                    const card = document.createElement("div");
                    card.className = "min-w-[360px] max-w-[360px] h-[500px] bg-white p-6 rounded-2xl shadow-lg border border-gray-300 flex-shrink-0 flex flex-col justify-between";

                    card.innerHTML = `
                        <h3 class="text-xl font-bold text-gray-800 mb-2">${producto.name}</h3>
                        <img src="/storage/obras/${producto.imagen}" alt="${producto.name}" class="w-full h-48 object-cover rounded mb-2"/>
                        <p class="text-gray-600">${producto.description}</p>
                        <p class="text-sm text-gray-500 mt-1">Tamaño: ${producto.height} x ${producto.width}</p>
                        <p class="text-sm text-gray-500">Categoría: ${producto.category}</p>
                    `;
                    contenedor.appendChild(card);
                });
                currentIndex = 0;
                updateCarousel();
            });
    };

    const updateCarousel = () => {
        const offset = currentIndex * 380; // nuevo ancho con margen

        contenedor.style.transform = `translateX(-${offset}px)`;
    };

    btnNext.addEventListener("click", () => {
        const max = contenedor.children.length - 3;
        if (currentIndex < max) {
            currentIndex++;
            updateCarousel();
        }
    });

    btnPrev.addEventListener("click", () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

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
