@extends('layouts.app')

@section('title', "Confirmar contraseña · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-5 py-16">
    <div class="w-full max-w-md lamina">
        <h1 class="text-2xl font-bold text-center text-tinta mb-2">Confirmar contraseña</h1>
        <p class="text-sm text-tinta-suave text-center mb-6">Por seguridad, vuelve a escribir tu contraseña antes de continuar.</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-6">
                <label for="password" class="etiqueta">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="campo">
                @error('password')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primario w-full">
                Confirmar
            </button>

            @if (Route::has('password.request'))
                <div class="text-center mt-4">
                    <a class="enlace text-sm" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
