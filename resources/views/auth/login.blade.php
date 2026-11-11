@extends('layouts.app')

@section('title', "Iniciar sesión · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-5 py-16">
    <div class="w-full max-w-md lamina">
        <h1 class="font-display text-4xl text-center mb-8">Iniciar Sesión</h1>

        @if(session('status'))
            <div class="aviso aviso-ok mb-4">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="etiqueta">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="campo"
                    required autofocus>
                @error('email')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="etiqueta">Contraseña</label>
                <input id="password" type="password" name="password"
                    class="campo"
                    required>
                @error('password')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember -->
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-acento focus:ring-acento/20 border-linea rounded-md"
                    {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="ml-2 block text-sm text-tinta">Recordarme</label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="btn btn-primario w-full">
                    Entrar
                </button>
            </div>

            @if (Route::has('password.request'))
                <div class="text-center mt-4">
                    <a class="enlace text-sm" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
        </form>

        <p class="mt-8 pt-6 border-t border-linea text-center text-sm text-tinta-suave">
            ¿Aún no tienes cuenta? <a href="{{ route('register') }}" class="enlace">Crea una</a> para pedir presupuestos.
        </p>
    </div>
</div>
@endsection
