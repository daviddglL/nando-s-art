{{-- Página de error con el estilo de la web. 404 y 403 la usan con el layout normal. --}}
@extends('layouts.app')
@section('title', "{$titulo} · Nando's Art")

@section('content')
<div class="flex flex-col items-center justify-center text-center min-h-[70vh] px-5 py-16">
    <p class="font-display italic text-[9rem] leading-none text-acento/25 mb-2">{{ $codigo }}</p>
    <h1 class="titulo-seccion mb-3">{{ $titulo }}</h1>
    <p class="text-tinta-suave mb-8 max-w-md">{{ $mensaje }}</p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="{{ route('galeria') }}" class="btn btn-primario">Ir a la galería</a>
        <a href="{{ route('landing') }}" class="btn btn-secundario">Inicio</a>
    </div>
</div>
@endsection
