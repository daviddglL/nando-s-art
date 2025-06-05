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

            <!-- Dirección -->
            <div class="col-span-1">
                <label for="address" class="block text-sm font-semibold text-gray-700">Dirección</label>
                <input id="address" type="text" name="address" value="{{ old('address') }}"
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">
                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Ciudad -->
            <div class="col-span-1">
                <label for="city" class="block text-sm font-semibold text-gray-700">Ciudad</label>
                <input id="city" type="text" name="city" value="{{ old('city') }}"
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror">
                @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Estado -->
            <div class="col-span-1">
                <label for="state" class="block text-sm font-semibold text-gray-700">Provincia/Estado</label>
                <input id="state" type="text" name="state" value="{{ old('state') }}"
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('state') border-red-500 @enderror">
                @error('state') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- País -->
            <div class="col-span-1">
                <label for="country" class="block text-sm font-semibold text-gray-700">País</label>
                <input id="country" type="text" name="country" value="{{ old('country') }}"
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('country') border-red-500 @enderror">
                @error('country') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Código postal -->
            <div class="col-span-1">
                <label for="zip" class="block text-sm font-semibold text-gray-700">Código postal</label>
                <input id="zip" type="text" name="zip" value="{{ old('zip') }}"
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('zip') border-red-500 @enderror">
                @error('zip') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Teléfono -->
            <div class="col-span-1">
                <label for="phone" class="block text-sm font-semibold text-gray-700">Teléfono</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                    class="mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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
