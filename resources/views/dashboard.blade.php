@extends('layouts.app')
@section('title', "Mis presupuestos · Nando's Art")

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-purple-800">Mis presupuestos</h1>
        <a href="{{ route('encargos.create') }}"
           class="bg-linear-to-r from-blue-200 to-pink-200 text-gray-700 px-5 py-2 rounded-lg shadow-sm font-semibold hover:scale-105 transition">
            + Solicitar presupuesto
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-4 overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-blue-50 text-gray-700">
                    <th class="py-3 px-2">Fecha</th>
                    <th class="py-3 px-2">Descripción</th>
                    <th class="py-3 px-2">Tamaño</th>
                    <th class="py-3 px-2">Estilo</th>
                    <th class="py-3 px-2">Categoría</th>
                    <th class="py-3 px-2">Estado</th>
                    <th class="py-3 px-2">Respuesta</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($encargos as $encargo)
                    <tr class="border-b last:border-0">
                        <td class="py-2 px-2 whitespace-nowrap">{{ $encargo->created_at->format('d/m/Y') }}</td>
                        <td class="py-2 px-2">{{ Str::limit($encargo->description, 80) }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $encargo->height }} x {{ $encargo->width }}</td>
                        <td class="py-2 px-2">{{ $encargo->styleEnum()?->label() ?? $encargo->style }}</td>
                        <td class="py-2 px-2">{{ $encargo->categoryEnum()?->label() ?? $encargo->category }}</td>
                        <td class="py-2 px-2"><x-estado-encargo :estado="$encargo->status" /></td>
                        <td class="py-2 px-2 whitespace-nowrap">
                            <a href="{{ route('encargos.show', $encargo) }}" class="text-blue-600 font-semibold hover:underline">
                                {{ $encargo->respuesta || $encargo->precio !== null ? 'Ver respuesta' : 'Ver detalle' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-600 py-4">Todavía no has solicitado ningún presupuesto.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
