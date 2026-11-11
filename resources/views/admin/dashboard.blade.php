@extends('layouts.app')
@section('title', "Panel · Nando's Art")

@section('content')
<div class="max-w-4xl mx-auto my-10 px-4">
    <h1 class="titulo-seccion mb-8">Hola, {{ $user->name }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('admin.obras.index') }}" class="block lamina sm:p-7 hover:-translate-y-0.5 hover:shadow-flotante transition">
            <span class="block text-3xl font-bold text-tinta">{{ $resumen['obras'] }}</span>
            <span class="text-tinta-suave">obras en la galería</span>
        </a>
        <a href="{{ route('encargos.index', ['estado' => 'pending']) }}" class="block lamina sm:p-7 hover:-translate-y-0.5 hover:shadow-flotante transition">
            <span class="block text-3xl font-bold text-acento">{{ $resumen['pendientes'] }}</span>
            <span class="text-tinta-suave">presupuestos sin responder</span>
        </a>
        <a href="{{ route('encargos.index') }}" class="block lamina sm:p-7 hover:-translate-y-0.5 hover:shadow-flotante transition">
            <span class="block text-3xl font-bold text-tinta">{{ $resumen['encargos'] }}</span>
            <span class="text-tinta-suave">presupuestos en total</span>
        </a>
    </div>

    <a href="{{ route('products.create') }}" class="btn btn-primario">
        + Nueva obra
    </a>
    <a href="{{ route('admin.paginas.index') }}" class="inline-block ml-2 py-2 px-4 rounded-lg border border-linea bg-papel-claro text-tinta-suave hover:bg-papel font-bold shadow-sm">
        Editar «Sobre mí» y «Contacto»
    </a>
</div>
@endsection
