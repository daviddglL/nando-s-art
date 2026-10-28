@extends('layouts.app')

@section('title', "Nueva contraseña · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-4rem)] px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Nueva contraseña</h2>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña nueva</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password-confirm" class="block text-sm font-medium text-gray-700">Repite la contraseña</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition">
                Guardar contraseña
            </button>
        </form>
    </div>
</div>
@endsection
