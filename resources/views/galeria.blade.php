@extends('layouts.app')
@use('App\Enums\Style')
@section('content')
<main id="galeria-main" class="flex flex-col items-center min-h-screen bg-gray-100 transition-colors duration-500">
    <h2 class="text-3xl font-bold text-gray-800 pt-12 mb-4">Muestras</h2>

    <!-- Estilos -->
    <div class="flex space-x-2 mb-4">
        @foreach(Style::cases() as $style)
            <button
                class="px-4 py-2 font-semibold rounded-sm filtro-estilo bg-gray-200 text-gray-800"
                data-style="{{ $style->value }}"
            >
                {{ $style->label() }}
            </button>
        @endforeach
    </div>

    <!-- Carrusel -->
    <div class="relative w-[1150px] rounded-xl px-4 py-6 overflow-hidden">

        <div id="contenedor-muestras" class="flex transition-transform duration-300 ease-in-out space-x-4">
            <!-- Tarjetas dinámicas aquí -->
        </div>

        <!-- Flechas -->
        <button id="prev" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white shadow-sm p-2 rounded-full hover:bg-gray-100">
            ◀
        </button>
        <button id="next" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white shadow-sm p-2 rounded-full hover:bg-gray-100">
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
<script>
document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".filtro-estilo");
    const contenedor = document.getElementById("contenedor-muestras");
    const btnNext = document.getElementById("next");
    const btnPrev = document.getElementById("prev");
    const main = document.getElementById("galeria-main");

    // Colores de cada estilo: salen del enum App\Enums\Style
    const fondos = @json(collect(Style::cases())->mapWithKeys(fn ($s) => [$s->value => $s->background()]));
    const coloresBtn = @json(collect(Style::cases())->mapWithKeys(fn ($s) => [$s->value => $s->buttonClasses()]));

    let productos = [];
    let currentIndex = 0;

    const cardWidth = 424; // 400px + mx-2 (2*12px)
    const visible = 5; // Cuántas tarjetas mostrar (debe ser impar)
    const half = Math.floor(visible / 2);

    function seleccionarBoton(estilo) {
        botones.forEach(boton => {
            // El seleccionado lleva su color pastel; el resto, gris
            const color = boton.dataset.style === estilo ? coloresBtn[estilo] : "bg-gray-200 text-gray-800";
            boton.className = "px-4 py-2 font-semibold rounded-sm filtro-estilo " + color;
            boton.setAttribute("aria-pressed", boton.dataset.style === estilo);
        });
    }

    const cargarEstilo = (estilo) => {
        // Cambia el fondo
        main.className = "flex flex-col items-center min-h-screen transition-colors duration-500 " + (fondos[estilo] || "bg-gray-100");
        // Marca el botón seleccionado
        seleccionarBoton(estilo);

        fetch(`/api/products?style=${encodeURIComponent(estilo)}`)
            .then(response => response.json())
            .then(data => {
                productos = data;
                currentIndex = Math.floor(productos.length / 2); // Empieza centrado
                renderCarrusel(productos, currentIndex);
            });
    };

    const crearElemento = (etiqueta, clases, texto) => {
        const el = document.createElement(etiqueta);
        el.className = clases;
        el.textContent = texto;
        return el;
    };

    const crearImagen = (producto) => {
        const img = document.createElement("img");
        img.src = `/storage/obras/${encodeURIComponent(producto.imagen)}`;
        img.alt = producto.name;
        img.loading = "lazy";
        img.className = "w-full h-48 object-cover rounded-sm mb-2";
        return img;
    };

    const renderCarrusel = (data, indexCentral) => {
        contenedor.replaceChildren();
        const total = data.length;
        if (total === 0) {
            contenedor.append(crearElemento("p", "w-full text-center text-gray-600 py-12", "Todavía no hay obras de este estilo."));
            contenedor.style.transform = "none";
            return;
        }
        const visible = Math.min(5, total); // máximo 5 visibles
        const half = Math.floor(visible / 2);

        // Espaciador izquierdo
        const spacerLeft = document.createElement("div");
        spacerLeft.className = "shrink-0";
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
            card.className = `min-w-[400px] max-w-[400px] h-[520px] bg-white p-6 rounded-2xl border border-gray-300 shrink-0 flex flex-col justify-between mx-2 transition-all duration-500 ease-in-out ${extraClasses}`;
            // Se construye con textContent para que el texto de las obras nunca se interprete como HTML
            card.append(
                crearElemento("h3", "text-xl font-bold text-gray-800 mb-2", producto.name),
                crearImagen(producto),
                crearElemento("p", "text-gray-600", producto.description),
                crearElemento("p", "text-sm text-gray-500 mt-1", `Tamaño: ${producto.height} x ${producto.width}`),
                crearElemento("p", "text-sm text-gray-500", `Categoría: ${producto.category}`)
            );
            contenedor.appendChild(card);
        });

        // Espaciador derecho
        const spacerRight = document.createElement("div");
        spacerRight.className = "shrink-0";
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
    cargarEstilo(@json(Style::Acuarela->value));

    botones.forEach(boton => {
        boton.addEventListener("click", () => {
            cargarEstilo(boton.dataset.style);
        });
    });
});
</script>
@endsection
