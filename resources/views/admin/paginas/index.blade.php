@extends('layouts.app')
@section('title', "Páginas · Nando's Art")

@section('content')
<div class="max-w-4xl mx-auto my-10 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Páginas</h1>
    <p class="text-gray-600 mb-6">Textos de las páginas «Sobre mí» y «Contacto» que ve cualquier visitante.</p>

    <ul class="space-y-4">
        @foreach ($paginas as $pagina)
            <li class="bg-white p-6 rounded-xl shadow-md flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $pagina->titulo }}</h2>
                    <p class="text-gray-600 truncate">{{ $pagina->resumen() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Última edición: {{ $pagina->updated_at?->diffForHumans() }}</p>
                </div>
                <div class="flex gap-3 shrink-0">
                    <a href="{{ $pagina->url() }}" class="py-2 px-4 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Ver</a>
                    <a href="{{ route('admin.paginas.edit', $pagina) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">Editar</a>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
