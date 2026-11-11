@extends('layouts.app')

@section('title', "Verifica tu email · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[70vh] px-5 py-16">
    <div class="w-full max-w-md lamina">
        <h1 class="font-display text-4xl text-center mb-8">Verifica tu email</h1>

        @if (session('resent'))
            <p class="aviso aviso-ok mb-4" role="status">
                Te hemos enviado un enlace nuevo de verificación.
            </p>
        @endif

        <p class="text-tinta-suave mb-6">
            Antes de pedir presupuestos, abre el enlace que te hemos enviado a
            <strong>{{ Auth::user()->email }}</strong> para confirmar que el email es tuyo.
        </p>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit"
                class="btn btn-primario w-full">
                Reenviar el enlace
            </button>
        </form>

        <p class="text-sm text-tinta-suave mt-4 text-center">
            ¿El email no es correcto? Cámbialo en <a href="{{ route('profile') }}" class="enlace">Mi perfil</a>.
        </p>
    </div>
</div>
@endsection
