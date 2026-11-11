<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Nando's Art")</title>

    {{-- SEO y vista previa al compartir en redes (cada página puede cambiar título, descripción e imagen) --}}
    <meta name="description" content="@yield('description', 'Galería de Nando\'s Art: acuarela, óleo, rotulador y más. Obras originales y encargos personalizados.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="Nando's Art">
    <meta property="og:locale" content="es_ES">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', "Nando's Art")">
    <meta property="og:description" content="@yield('description', 'Galería de Nando\'s Art: acuarela, óleo, rotulador y más. Obras originales y encargos personalizados.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/mujer-960.webp'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#f7f2ea">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
@php
    // Enlaces del menú principal: [ruta, texto, rutas que lo marcan como página actual]
    $menu = [
        ['landing', 'Inicio', ['landing']],
        ['galeria', 'Galería', ['galeria', 'obras.*']],
        ['paginas.sobre-mi', 'Sobre mí', ['paginas.sobre-mi']],
        ['paginas.contacto', 'Contacto', ['paginas.contacto']],
    ];
    $panel = [
        ['admin', 'Resumen', ['admin']],
        ['admin.obras.index', 'Obras', ['admin.obras.*', 'products.create']],
        ['encargos.index', 'Presupuestos', ['encargos.index']],
        ['admin.paginas.index', 'Páginas', ['admin.paginas.*']],
    ];
@endphp
<body class="min-h-screen flex flex-col bg-papel text-tinta">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 btn btn-primario">Saltar al contenido</a>

    {{-- Barra del estudio: solo la ve el artista, con los atajos del panel --}}
    @if (Auth::user()?->isAdmin())
        <div class="bg-noche text-papel/80 text-xs">
            <div class="contenedor flex flex-wrap items-center gap-x-5 gap-y-1 py-2">
                <span class="font-semibold tracking-[0.2em] uppercase text-acento-claro">Estudio</span>
                @foreach ($panel as [$ruta, $texto, $activas])
                    <a href="{{ route($ruta) }}" @if (request()->routeIs(...$activas)) aria-current="page" class="text-white" @else class="hover:text-white" @endif>{{ $texto }}</a>
                @endforeach
                <a href="{{ route('products.create') }}" class="ml-auto hover:text-white">+ Nueva obra</a>
            </div>
        </div>
    @endif

    <header class="sticky top-0 z-40 border-b border-linea/80 bg-papel/85 backdrop-blur-md supports-[backdrop-filter]:bg-papel/70">
        <nav class="contenedor flex flex-wrap items-center justify-between gap-y-3 py-4" aria-label="Principal">
            <a href="{{ route('landing') }}" class="group flex items-baseline gap-2" aria-label="Nando's Art, inicio">
                <span class="font-display text-2xl font-semibold italic tracking-tight">Nando's Art</span>
                <span class="hidden sm:inline text-[11px] font-semibold tracking-[0.25em] uppercase text-tinta-tenue group-hover:text-acento transition">Fernando Delgado</span>
            </a>

            {{-- En móvil el menú se pliega; lo abre resources/js/menu.js --}}
            <button type="button" class="md:hidden -mr-2 p-2 rounded-full text-tinta hover:bg-papel-oscuro"
                    data-menu-boton aria-controls="menu-principal" aria-expanded="false" aria-label="Abrir menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h10"/>
                </svg>
            </button>

            <ul id="menu-principal"
                class="hidden w-full md:w-auto md:flex flex-col md:flex-row gap-4 md:gap-8 md:items-center border-t border-linea md:border-0 pt-4 md:pt-0">
                @foreach ($menu as [$ruta, $texto, $activas])
                    <li>
                        <a href="{{ route($ruta) }}" class="nav-enlace" @if (request()->routeIs(...$activas)) aria-current="page" @endif>{{ $texto }}</a>
                    </li>
                @endforeach
                @guest
                    <li>
                        <a href="{{ route('login') }}" class="nav-enlace" @if (request()->routeIs('login')) aria-current="page" @endif>Iniciar sesión</a>
                    </li>
                    <li>
                        <a href="{{ route('encargos.create') }}" class="btn btn-primario btn-pequeno">Pedir un encargo</a>
                    </li>
                @endguest
                @auth
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-enlace" @if (request()->routeIs('dashboard', 'encargos.show', 'encargos.create')) aria-current="page" @endif>Mis presupuestos</a>
                    </li>
                    <li>
                        <a href="{{ route('profile') }}" class="nav-enlace" @if (request()->routeIs('profile')) aria-current="page" @endif>Mi perfil</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-enlace">Cerrar sesión</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>
    </header>

    {{-- Mensajes de éxito de cualquier acción (redirect()->with('success', ...)) --}}
    @if (session('success'))
        <div class="contenedor mt-6">
            <p role="status" class="aviso aviso-ok">{{ session('success') }}</p>
        </div>
    @endif

    <main id="contenido" class="flex-1" tabindex="-1">
        @yield('content')
    </main>

    <x-footer />
    @yield('scripts')
</body>
</html>
