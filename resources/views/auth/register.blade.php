@extends('layouts.app')

@section('title', "Crear cuenta · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-5 py-16">
    <div class="w-full max-w-3xl lamina">
        <h1 class="font-display text-4xl text-center mb-8">Registro de Usuario</h1>

        <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <!-- Nombre -->
            <div class="col-span-1">
                <label for="name" class="etiqueta">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="campo @error('name') border-red-500 @enderror">
                @error('name') <p class="error-campo">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div class="col-span-1">
                <label for="email" class="etiqueta">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="campo @error('email') border-red-500 @enderror">
                @error('email') <p class="error-campo">{{ $message }}</p> @enderror
            </div>

            <!-- Contraseña -->
            <div class="col-span-1">
                <label for="password" class="etiqueta">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="campo @error('password') border-red-500 @enderror">
                @error('password') <p class="error-campo">{{ $message }}</p> @enderror
            </div>

            <!-- Confirmar contraseña -->
            <div class="col-span-1">
                <label for="password-confirm" class="etiqueta">Confirmar contraseña</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    class="campo">
            </div>

            {{-- Campo trampa para bots: invisible y fuera del orden de tabulación --}}
            <div class="hidden" aria-hidden="true">
                <label for="web">Deja este campo vacío</label>
                <input id="web" type="text" name="web" value="" tabindex="-1" autocomplete="off">
            </div>
            @error('web') <p class="col-span-2 text-red-700 text-sm">{{ $message }}</p> @enderror

            <!-- Botón de registro -->
            <div class="col-span-2">
                <button type="submit"
                    class="btn btn-primario w-full">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
