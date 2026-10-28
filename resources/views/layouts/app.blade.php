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
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', "Nando's Art")">
    <meta property="og:description" content="@yield('description', 'Galería de Nando\'s Art: acuarela, óleo, rotulador y más. Obras originales y encargos personalizados.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/mujer.webp'))">
    <meta name="twitter:card" content="summary_large_image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="body-app" class="bg-gray-100 min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-linear-to-r from-blue-100 to-pink-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap justify-between items-center py-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-gray-700">Nando's Art</a>

            {{-- En móvil el menú se pliega; lo abre resources/js/menu.js --}}
            <button type="button" class="md:hidden p-2 rounded-sm text-gray-700 hover:bg-white/50"
                    data-menu-boton aria-controls="menu-principal" aria-expanded="false" aria-label="Abrir menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <ul id="menu-principal"
                class="hidden w-full md:w-auto md:flex flex-col md:flex-row gap-3 md:gap-6 md:items-center pt-4 md:pt-0">
                <li>
                    <a href="{{ route('landing') }}" class="text-gray-700 hover:text-blue-500 font-medium">Inicio</a>
                </li>
                <li>
                    <a href="{{ route('galeria') }}" class="text-blue-500 font-semibold hover:underline">Galería</a>
                </li>
                @guest
                    <li>
                        <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">Iniciar Sesión</a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="inline-block bg-pink-200 text-gray-700 px-3 py-1 rounded-sm hover:bg-pink-300 font-medium transition">Crear cuenta</a>
                    </li>
                @endguest
                @auth
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-blue-500 font-semibold hover:underline">Mis presupuestos</a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('admin') }}" class="text-blue-500 font-semibold hover:underline">Panel</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.obras.index') }}" class="text-blue-500 font-semibold hover:underline">Obras</a>
                        </li>
                        <li>
                            <a href="{{ route('encargos.index') }}" class="text-blue-500 font-semibold hover:underline">
                                Presupuestos recibidos
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('profile') }}" class="text-blue-500 font-semibold hover:underline">Mi perfil</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-500 font-semibold hover:underline bg-transparent border-none cursor-pointer p-0">Cerrar sesión</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    {{-- Mensajes de éxito de cualquier acción (redirect()->with('success', ...)) --}}
    @if (session('success'))
        <div class="max-w-5xl mx-auto mt-4 px-4">
            <p role="status" class="bg-green-100 text-green-800 p-3 rounded-sm">{{ session('success') }}</p>
        </div>
    @endif

    <!-- CONTENIDO PRINCIPAL -->
    <main>
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
