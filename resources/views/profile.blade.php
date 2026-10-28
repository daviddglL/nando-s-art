@extends('layouts.app')

@section('title', "Mi perfil · Nando's Art")

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4 space-y-6">
    <h1 class="text-3xl font-bold text-purple-800">Mi perfil</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-sm" role="status">{{ session('success') }}</div>
    @endif

    {{-- Datos de la cuenta --}}
    <section class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Datos de la cuenta</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('name', 'updateProfile')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('email', 'updateProfile')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="py-2 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition">
                Guardar
            </button>
        </form>
    </section>

    {{-- Cambiar contraseña --}}
    <section class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Cambiar contraseña</h2>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña actual</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('current_password', 'updatePassword')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña nueva</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('password', 'updatePassword')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Repite la contraseña nueva</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit" class="py-2 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition">
                Cambiar contraseña
            </button>
        </form>
    </section>

    {{-- Borrar la cuenta --}}
    <section class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Borrar la cuenta</h2>
        <p class="text-sm text-gray-600 mb-4">Se borrarán tu cuenta y tus presupuestos. No se puede deshacer.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4"
            onsubmit="return confirm('¿Seguro que quieres borrar tu cuenta?')">
            @csrf
            @method('DELETE')

            <div>
                <label for="delete_password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input id="delete_password" type="password" name="password" required autocomplete="current-password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('password', 'deleteUser')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="py-2 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition">
                Borrar mi cuenta
            </button>
        </form>
    </section>
</div>
@endsection
