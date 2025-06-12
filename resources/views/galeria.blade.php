<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería</title>
</head>
<body class="bg-gray-100">



{{-- filepath: c:\Users\ragna\OneDrive\Escritorio\nando-s-art\resources\views\galeria.blade.php --}}
@extends('layouts.app')
@section('content')
<main id="galeria-main" class="flex flex-col items-center min-h-screen bg-gray-100 transition-colors duration-500">
    <h2 class="text-3xl font-bold text-gray-800 pt-12 mb-4">Muestras</h2>

    <!-- Estilos -->
    @php
        $colores = [
            'acuarela' => 'bg-blue-100 text-blue-900 hover:bg-blue-200',
            'rotulador' => 'bg-yellow-100 text-yellow-900 hover:bg-yellow-200',
            'oleo' => 'bg-pink-100 text-pink-900 hover:bg-pink-200',
            'sketch' => 'bg-gray-100 text-gray-900 hover:bg-gray-200',
            'papel' => 'bg-green-100 text-green-900 hover:bg-green-200',
            'lienzo' => 'bg-purple-100 text-purple-900 hover:bg-purple-200',
        ];
    @endphp

    <div class="flex space-x-2 mb-4">
        @foreach(['acuarela', 'rotulador', 'oleo', 'sketch', 'papel', 'lienzo'] as $style)
            <button 
                class="px-4 py-2 font-semibold rounded filtro-estilo bg-gray-200 text-gray-800"
                data-style="{{ $style }}"
                data-color="{{ $colores[$style] }}"
            >
                {{ ucfirst($style) }}
            </button>
        @endforeach
    </div>

    <!-- Carrusel -->
    <div class="relative w-[1150px] rounded-xl px-4 py-6 overflow-hidden">

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
<footer class="w-full  bg-gradient-to-r from-blue-200 via-purple-100 to-pink-200 py-6 shadow-inner">
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between px-6">
        <div class="text-gray-700 text-center md:text-left mb-2 md:mb-0">
            © {{ date('Y') }} Nando's Art. Todos los derechos reservados.
        </div>
        <div class="flex space-x-4 justify-center">
            <a href="https://www.instagram.com/nandomtgalters/" target="_blank" class="text-gray-600 hover:text-pink-500 transition-colors">
                <svg class="w-6 h-6 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5zm4.25 3.25a5.25 5.25 0 1 1 0 10.5 5.25 5.25 0 0 1 0-10.5zm0 1.5a3.75 3.75 0 1 0 0 7.5 3.75 3.75 0 0 0 0-7.5zm6 1.25a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
            </a>
        </div>
    </div>
</footer>
@endsection

@yield('scripts')

<script>
document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-estilo");
    const contenedor = document.getElementById("contenedor-muestras");
    const btnNext = document.getElementById("next");
    const btnPrev = document.getElementById("prev");
    const main = document.getElementById("galeria-main");

    // Relación estilo -> clase de fondo Tailwind
    const fondos = {
        acuarela: "bg-blue-100",
        rotulador: "bg-yellow-100",
        oleo: "bg-pink-100",
        sketch: "bg-gray-100",
        papel: "bg-green-100",
        lienzo: "bg-purple-100"
    };

    // Relación estilo -> clases de botón
    const coloresBtn = {
        acuarela: "bg-blue-100 text-blue-900 hover:bg-blue-200",
        rotulador: "bg-yellow-100 text-yellow-900 hover:bg-yellow-200",
        oleo: "bg-pink-100 text-pink-900 hover:bg-pink-200",
        sketch: "bg-gray-100 text-gray-900 hover:bg-gray-200",
        papel: "bg-green-100 text-green-900 hover:bg-green-200",
        lienzo: "bg-purple-100 text-purple-900 hover:bg-purple-200"
    };

    let productos = [];
    let currentIndex = 0;

    const cardWidth = 424; // 400px + mx-2 (2*12px)
    const visible = 5; // Cuántas tarjetas mostrar (debe ser impar)
    const half = Math.floor(visible / 2);

    function seleccionarBoton(estilo) {
        botones.forEach(boton => {
            // Quita el color a todos
            boton.className = "px-4 py-2 font-semibold rounded filtro-estilo bg-gray-200 text-gray-800";
            // Si es el seleccionado, ponle su color pastel
            if (boton.dataset.style === estilo) {
                boton.className += " " + coloresBtn[estilo];
            }
        });
    }

    const cargarEstilo = (estilo) => {
        // Cambia el fondo
        main.className = "flex flex-col items-center min-h-screen transition-colors duration-500 " + (fondos[estilo] || "bg-gray-100");
        // Marca el botón seleccionado
        seleccionarBoton(estilo);

        fetch(`/api/products?style=${estilo}`)
            .then(response => response.json())
            .then(data => {
                productos = data;
                currentIndex = Math.floor(productos.length / 2); // Empieza centrado
                renderCarrusel(productos, currentIndex);
            });
    };

    const renderCarrusel = (data, indexCentral) => {
        contenedor.innerHTML = "";
        const total = data.length;
        const visible = Math.min(5, total); // máximo 5 visibles
        const half = Math.floor(visible / 2);

        // Espaciador izquierdo
        const spacerLeft = document.createElement("div");
        spacerLeft.className = "flex-shrink-0";
        spacerLeft.style.width = `${cardWidth}px`;
        contenedor.appendChild(spacerLeft);

        // Calcula los índices de los productos a mostrar (en bucle)
        let indices = [];
        for (let i = -half; i <= half; i++) {
            let idx = (indexCentral + i + total) % total;
            indices.push(idx);
        }

        indices.forEach((idx, pos) => {
            const producto = data[idx];
            let extraClasses = "";
            if (pos === half) {
                extraClasses = "scale-110 z-20 shadow-2xl";
            } else if (pos === half - 1 || pos === half + 1) {
                extraClasses = "scale-95 blur-[1px] z-10";
            } else {
                extraClasses = "scale-90 blur-[2px] opacity-60 z-0";
            }

            const card = document.createElement("div");
            card.className = `min-w-[400px] max-w-[400px] h-[520px] bg-white p-6 rounded-2xl border border-gray-300 flex-shrink-0 flex flex-col justify-between mx-2 transition-all duration-500 ease-in-out ${extraClasses}`;
            card.innerHTML = `
                <h3 class="text-xl font-bold text-gray-800 mb-2">${producto.name}</h3>
                <img src="/storage/obras/${producto.imagen}" alt="${producto.name}" class="w-full h-48 object-cover rounded mb-2"/>
                <p class="text-gray-600">${producto.description}</p>
                <p class="text-sm text-gray-500 mt-1">Tamaño: ${producto.height} x ${producto.width}</p>
                <p class="text-sm text-gray-500">Categoría: ${producto.category}</p>
            `;
            contenedor.appendChild(card);
        });

        // Espaciador derecho
        const spacerRight = document.createElement("div");
        spacerRight.className = "flex-shrink-0";
        spacerRight.style.width = `${cardWidth}px`;
        contenedor.appendChild(spacerRight);

        // Centra el carrusel visualmente
        const offset = cardWidth * (half + 1.5); // +1 por el espaciador izquierdo
        contenedor.style.transition = "transform 0.7s cubic-bezier(0.4, 0, 0.2, 1)";
        contenedor.style.transform = `translateX(calc(50% - ${offset}px))`;
    };

    // Bucle infinito en las flechas
    btnNext.addEventListener("click", () => {
        if (productos.length === 0) return;
        currentIndex = (currentIndex + 1) % productos.length;
        renderCarrusel(productos, currentIndex);
    });

    btnPrev.addEventListener("click", () => {
        if (productos.length === 0) return;
        currentIndex = (currentIndex - 1 + productos.length) % productos.length;
        renderCarrusel(productos, currentIndex);
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
