<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Nando's Art")</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="body-app" class="bg-gray-100 min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-linear-to-r from-blue-100 to-pink-100 shadow-sm ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-700">
                <a href="{{ url('/') }}">Nando's Art</a>
            </h1>
            <ul class="flex space-x-6 items-center">
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
                        <a href="{{ route('register') }}" class="bg-pink-200 text-gray-700 px-3 py-1 rounded-sm hover:bg-pink-300 font-medium transition">Crear cuenta</a>
                    </li>
                @endguest
                @auth
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-blue-500 font-semibold hover:underline">Mis presupuestos</a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('shipments.index') }}" class="text-blue-500 font-semibold hover:underline">
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
                            <button type="submit" class="text-red-500 font-semibold hover:underline bg-transparent border-none cursor-pointer">Cerrar sesión</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="">
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
