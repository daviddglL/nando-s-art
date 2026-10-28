@extends('layouts.app')

@section('title', "Recuperar contraseña · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-4rem)] px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Recuperar contraseña</h2>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-2 rounded-sm mb-4 text-sm" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-xs focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition">
                Enviar enlace para restablecerla
            </button>
        </form>
    </div>
</div>
@endsection
