{{-- Página de error con el estilo de la web. 404 y 403 la usan con el layout normal. --}}
@extends('layouts.app')
@section('title', "{$titulo} · Nando's Art")

@section('content')
<div class="flex flex-col items-center justify-center text-center min-h-[70vh] px-4 bg-linear-to-br from-blue-100 via-purple-100 to-pink-100">
    <p class="text-7xl font-extrabold text-gray-300 mb-4">{{ $codigo }}</p>
    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $titulo }}</h1>
    <p class="text-gray-700 mb-8 max-w-md">{{ $mensaje }}</p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="{{ route('galeria') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow-sm">Ir a la galería</a>
        <a href="{{ route('landing') }}" class="bg-white text-gray-700 font-semibold py-2 px-5 rounded-lg shadow-sm hover:bg-gray-50">Inicio</a>
    </div>
</div>
@endsection
