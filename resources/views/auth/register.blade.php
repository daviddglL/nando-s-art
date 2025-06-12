@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-100 via-white to-blue-200">
    <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg p-10">
        <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-8">Registro de Usuario</h2>

        <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <!-- Nombre -->
            <div class="col-span-1">
                <label for="name" class="block text-sm font-semibold text-gray-700">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div class="col-span-1">
                <label for="email" class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Contraseña -->
            <div class="col-span-1">
                <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Confirmar contraseña -->
            <div class="col-span-1">
                <label for="password-confirm" class="block text-sm font-semibold text-gray-700">Confirmar contraseña</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

        

            <!-- Botón de registro -->
            <div class="col-span-2">
                <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold py-3 rounded-md hover:bg-blue-700 transition">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
