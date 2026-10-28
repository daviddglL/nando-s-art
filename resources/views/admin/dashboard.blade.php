@extends('layouts.app')
@section('title', "Panel · Nando's Art")

@section('content')
<div class="max-w-4xl mx-auto my-10 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Hola, {{ $user->name }}</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('admin.obras.index') }}" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition">
            <span class="block text-3xl font-bold text-gray-800">{{ $resumen['obras'] }}</span>
            <span class="text-gray-600">obras en la galería</span>
        </a>
        <a href="{{ route('encargos.index', ['estado' => 'pending']) }}" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition">
            <span class="block text-3xl font-bold text-pink-700">{{ $resumen['pendientes'] }}</span>
            <span class="text-gray-600">presupuestos sin responder</span>
        </a>
        <a href="{{ route('encargos.index') }}" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition">
            <span class="block text-3xl font-bold text-gray-800">{{ $resumen['encargos'] }}</span>
            <span class="text-gray-600">presupuestos en total</span>
        </a>
    </div>

    <a href="{{ route('products.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
        + Nueva obra
    </a>
</div>
@endsection
