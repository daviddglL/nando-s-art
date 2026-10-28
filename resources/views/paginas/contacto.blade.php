@extends('layouts.app')
@section('title', "{$pagina->titulo} · Nando's Art")
@section('description', $pagina->resumen())

@section('content')
<article class="bg-linear-to-br from-blue-100 via-purple-100 to-pink-100 py-16 px-4">
    <div class="max-w-3xl mx-auto bg-white/80 rounded-xl shadow-lg p-6 sm:p-10">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-6 tracking-tight">{{ $pagina->titulo }}</h1>
        <div class="text-gray-700 text-lg leading-relaxed whitespace-pre-line">{{ $pagina->contenido }}</div>

        @if ($pagina->email || $pagina->telefono || $pagina->instagram || $pagina->ubicacion)
            <dl class="mt-8 grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-x-6 gap-y-3 text-gray-700">
                @if ($pagina->email)
                    <dt class="font-semibold">Email</dt>
                    <dd><a href="mailto:{{ $pagina->email }}" class="text-blue-600 hover:underline break-all">{{ $pagina->email }}</a></dd>
                @endif
                @if ($pagina->telefono)
                    <dt class="font-semibold">Teléfono</dt>
                    <dd><a href="{{ $pagina->telefonoEnlace() }}" class="text-blue-600 hover:underline">{{ $pagina->telefono }}</a></dd>
                @endif
                @if ($pagina->instagram)
                    <dt class="font-semibold">Instagram</dt>
                    <dd><a href="{{ $pagina->instagram }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline break-all">{{ '@'.trim(Str::after($pagina->instagram, 'instagram.com/'), '/') }}</a></dd>
                @endif
                @if ($pagina->ubicacion)
                    <dt class="font-semibold">Dónde estoy</dt>
                    <dd>{{ $pagina->ubicacion }}</dd>
                @endif
            </dl>
        @endif

        <div class="mt-10 p-6 rounded-lg bg-linear-to-r from-blue-200 via-purple-100 to-pink-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">¿Tienes una idea para un encargo?</h2>
            <p class="text-gray-700 mb-4">Cuéntamela con medidas, estilo y una imagen de referencia, y te respondo con un presupuesto.</p>
            <a href="{{ route('encargos.create') }}" class="inline-block bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-5 rounded-lg shadow-sm">Pedir presupuesto</a>
        </div>
    </div>
</article>

<x-footer />
@endsection
