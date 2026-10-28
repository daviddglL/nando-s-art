@extends('layouts.app')

@section('title', "Verifica tu email · Nando's Art")

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-4rem)] px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Verifica tu email</h1>

        @if (session('resent'))
            <p class="bg-green-100 text-green-800 p-2 rounded-sm mb-4 text-sm" role="status">
                Te hemos enviado un enlace nuevo de verificación.
            </p>
        @endif

        <p class="text-gray-700 mb-6">
            Antes de pedir presupuestos, abre el enlace que te hemos enviado a
            <strong>{{ Auth::user()->email }}</strong> para confirmar que el email es tuyo.
        </p>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition">
                Reenviar el enlace
            </button>
        </form>

        <p class="text-sm text-gray-600 mt-4 text-center">
            ¿El email no es correcto? Cámbialo en <a href="{{ route('profile') }}" class="text-blue-600 hover:underline">Mi perfil</a>.
        </p>
    </div>
</div>
@endsection
