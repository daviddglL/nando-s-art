@extends('layouts.app')

@section('title', "Recuperar contraseña · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-5 py-16">
    <div class="w-full max-w-md lamina">
        <h1 class="font-display text-4xl text-center mb-8">Recuperar contraseña</h1>

        @if (session('status'))
            <div class="aviso aviso-ok mb-4" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="etiqueta">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                    class="campo">
                @error('email')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="btn btn-primario w-full">
                Enviar enlace para restablecerla
            </button>
        </form>
    </div>
</div>
@endsection
