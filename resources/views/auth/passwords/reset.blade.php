@extends('layouts.app')

@section('title', "Nueva contraseña · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-5 py-16">
    <div class="w-full max-w-md lamina">
        <h1 class="font-display text-4xl text-center mb-8">Nueva contraseña</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label for="email" class="etiqueta">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                    class="campo">
                @error('email')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="etiqueta">Contraseña nueva</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="campo">
                @error('password')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password-confirm" class="etiqueta">Repite la contraseña</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="campo">
            </div>

            <button type="submit"
                class="btn btn-primario w-full">
                Guardar contraseña
            </button>
        </form>
    </div>
</div>
@endsection
