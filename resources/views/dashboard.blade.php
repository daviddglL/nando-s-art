@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-purple-800">Mis presupuestos</h1>
        <a href="{{ route('shipments.create') }}"
           class="bg-linear-to-r from-blue-200 to-pink-200 text-gray-700 px-5 py-2 rounded-lg shadow-sm font-semibold hover:scale-105 transition">
            + Solicitar presupuesto
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-4 overflow-x-auto">
        <table class="min-w-full text-center">
            <thead>
                <tr class="bg-blue-50 text-gray-700">
                    <th class="py-3 px-2">Descripción</th>
                    <th class="py-3 px-2">Tamaño</th>
                    <th class="py-3 px-2">Estilo</th>
                    <th class="py-3 px-2">Categoría</th>
                    <th class="py-3 px-2">Estado</th>
                    <th class="py-3 px-2">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shipments as $shipment)
                    <tr class="border-b last:border-0">
                        <td class="py-2 px-2">{{ $shipment->description }}</td>
                        <td class="py-2 px-2">{{ $shipment->height }} x {{ $shipment->width }}</td>
                        <td class="py-2 px-2">{{ $shipment->styleEnum()?->label() ?? $shipment->style }}</td>
                        <td class="py-2 px-2">{{ $shipment->categoryEnum()?->label() ?? $shipment->category }}</td>
                        <td class="py-2 px-2">{{ $shipment->status->label() }}</td>
                        <td class="py-2 px-2">{{ $shipment->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-pink-400 py-4">Todavía no has solicitado ningún presupuesto.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
