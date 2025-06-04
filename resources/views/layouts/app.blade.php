<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Mi aplicación</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
                {{ config('app.name', 'Laravel') }}
            </a>

            <div class="flex items-center space-x-4">
                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium">Login</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-blue-600 font-medium">Register</a>
                    @endif
                @else
                    <div class="relative group">
                        <button class="text-gray-800 font-semibold focus:outline-none">
                            {{ Auth::user()->name }}
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-10">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="py-8">
        @yield('content')
    </main>

</body>
</html>
