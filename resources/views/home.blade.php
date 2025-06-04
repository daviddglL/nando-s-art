

@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Bienvenido, {{ Auth::user()->name }}</h1>
    <p class="text-gray-600 mb-6">Rol: <span class="font-semibold text-blue-600">{{ Auth::user()->role }}</span></p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow">
            Cerrar sesión
        </button>
    </form>
</div>
@endsection
