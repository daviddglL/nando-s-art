<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body id="body-app" class="bg-gray-100 min-h-screen">

    <!-- NAVBAR -->


    {{-- Puedes poner esto en layouts.app o al inicio de tu vista index.blade.php --}}
    <nav class="bg-gradient-to-r from-blue-100 to-pink-100 shadow ">
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
                        <a href="{{ route('register') }}" class="bg-pink-200 text-gray-700 px-3 py-1 rounded hover:bg-pink-300 font-medium transition">Crear cuenta</a>
                    </li>
                @endguest
                @auth
                    @if(Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('shipments.index') }}" class="text-blue-500 font-semibold hover:underline">
                                Ver Shipments
                            </a>
                        </li>
                    @endif
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
