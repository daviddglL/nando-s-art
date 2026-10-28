@extends('layouts.app')
@use('App\Enums\EstadoEncargo')
@section('title', "Presupuestos recibidos · Nando's Art")

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Presupuestos recibidos</h1>
        <a href="{{ route('encargos.create') }}"
           class="bg-linear-to-r from-blue-200 to-pink-200 text-gray-700 px-5 py-2 rounded-lg shadow-sm font-semibold hover:scale-105 transition border border-blue-100">
            + Nuevo presupuesto
        </a>
    </div>

    {{-- Filtro por estado --}}
    <nav class="flex flex-wrap gap-2 mb-4" aria-label="Filtrar por estado">
        <a href="{{ route('encargos.index') }}" @class(['px-3 py-1 rounded-full border', 'bg-gray-800 text-white' => ! $estado, 'bg-white text-gray-700' => $estado])
           @if (! $estado) aria-current="page" @endif>Todos</a>
        @foreach (EstadoEncargo::cases() as $opcion)
            <a href="{{ route('encargos.index', ['estado' => $opcion->value]) }}"
               @class(['px-3 py-1 rounded-full border', 'bg-gray-800 text-white' => $estado === $opcion->value, 'bg-white text-gray-700' => $estado !== $opcion->value])
               @if ($estado === $opcion->value) aria-current="page" @endif>{{ $opcion->label() }}</a>
        @endforeach
    </nav>

    <div class="bg-white rounded-2xl shadow-lg p-4 overflow-x-auto">
        <table class="min-w-full text-base text-left">
            <thead>
                <tr class="bg-blue-50 text-gray-700">
                    <th class="py-3 px-2">Fecha</th>
                    <th class="py-3 px-2">Cliente</th>
                    <th class="py-3 px-2">Descripción</th>
                    <th class="py-3 px-2">Medidas</th>
                    <th class="py-3 px-2">Estilo</th>
                    <th class="py-3 px-2">Categoría</th>
                    <th class="py-3 px-2">Estado</th>
                    <th class="py-3 px-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($encargos as $encargo)
                    <tr class="border-b last:border-0">
                        <td class="py-2 px-2 whitespace-nowrap">{{ $encargo->created_at->format('d/m/Y') }}</td>
                        <td class="py-2 px-2">
                            {{ $encargo->user?->name }}
                            <span class="block text-sm text-gray-500">{{ $encargo->user?->email }}</span>
                        </td>
                        <td class="py-2 px-2">{{ Str::limit($encargo->description, 80) }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $encargo->height }} x {{ $encargo->width }}</td>
                        <td class="py-2 px-2">
                            <span class="{{ $encargo->styleEnum()?->textColor() ?? 'text-purple-500' }} font-semibold">
                                {{ $encargo->styleEnum()?->label() ?? $encargo->style }}
                            </span>
                        </td>
                        <td class="py-2 px-2">
                            <span class="{{ $encargo->categoryEnum()?->textColor() ?? 'text-gray-400' }} font-semibold">
                                {{ $encargo->categoryEnum()?->label() ?? $encargo->category }}
                            </span>
                        </td>
                        <td class="py-2 px-2"><x-estado-encargo :estado="$encargo->status" /></td>
                        <td class="py-2 px-2 text-right whitespace-nowrap">
                            <a href="{{ route('encargos.show', $encargo) }}"
                               class="inline-block bg-blue-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-blue-200">Responder</a>
                            <form action="{{ route('encargos.destroy', $encargo->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Seguro que quieres eliminar este presupuesto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-pink-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-pink-200">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-600 py-4 text-lg">No hay presupuestos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $encargos->links() }}</div>
</div>
@endsection
