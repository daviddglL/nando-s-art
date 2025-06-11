{{-- filepath: c:\Users\ragna\OneDrive\Escritorio\nando-s-art\resources\views\shipments\index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen py-8 px-2 md:px-8 bg-gray-50">
    <div class="flex flex-col md:flex-row gap-6 w-full h-full">
        <!-- Columna izquierda: Usuarios -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-2xl shadow-lg h-full flex flex-col">
                <div class="text-center py-4 rounded-t-2xl bg-gradient-to-r from-blue-200 to-pink-200">
                    <h4 class="text-gray-700 text-xl font-semibold mb-0">Usuarios</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-center">
                        <thead>
                            <tr class="bg-blue-50 text-gray-700">
                                <th class="py-2 px-2">ID</th>
                                <th class="py-2 px-2">Nombre</th>
                                <th class="py-2 px-2">Email</th>
                                <th class="py-2 px-2">Dirección</th>
                                <th class="py-2 px-2">Ciudad</th>
                                <th class="py-2 px-2">País</th>
                                <th class="py-2 px-2">ZIP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="py-1 px-2">{{ $user->id }}</td>
                                    <td class="py-1 px-2">{{ $user->name }}</td>
                                    <td class="py-1 px-2">{{ $user->email }}</td>
                                    <td class="py-1 px-2">{{ $user->address }}</td>
                                    <td class="py-1 px-2">{{ $user->city }}</td>
                                    <td class="py-1 px-2">{{ $user->country }}</td>
                                    <td class="py-1 px-2">{{ $user->zip }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-pink-400 py-4">No hay usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Columna derecha: Shipments -->
        <div class="w-full md:w-2/3">
            <div class="flex justify-end mb-4">
                <a href="{{ route('shipments.create') }}"
                   class="bg-gradient-to-r from-blue-200 to-pink-200 text-gray-700 px-5 py-2 rounded-lg shadow font-semibold hover:scale-105 transition border border-blue-100">
                    + Crear nuevo shipment
                </a>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-base text-center">
                        <thead>
                            <tr class="bg-blue-50 text-gray-700">
                                <th class="py-3 px-2">ID</th>
                                <th class="py-3 px-2">Descripción</th>
                                <th class="py-3 px-2">Alto</th>
                                <th class="py-3 px-2">Ancho</th>
                                <th class="py-3 px-2">Estilo</th>
                                <th class="py-3 px-2">Categoría</th>
                                <th class="py-3 px-2">Fecha</th>
                                <th class="py-3 px-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipments as $shipment)
                                @php
                                    // Colores pastel y legibles para estilos
                                    switch($shipment->style) {
                                        case 'Realista': $styleClass = 'text-emerald-600'; break;    // verde legible
                                        case 'Cartoon': $styleClass = 'text-yellow-600'; break;      // amarillo oscuro
                                        case 'Abstracto': $styleClass = 'text-sky-600'; break;       // azul claro legible
                                        case 'Minimalista': $styleClass = 'text-orange-500'; break;  // naranja legible
                                        default: $styleClass = 'text-purple-500';                    // violeta legible
                                    }
                                    // Colores pastel para categorías (puedes ajustar igual si lo deseas)
                                    switch($shipment->category) {
                                        case 'Retrato': $catClass = 'text-pink-400'; break;
                                        case 'Paisaje': $catClass = 'text-green-400'; break;
                                        case 'Animal': $catClass = 'text-indigo-400'; break;
                                        case 'Fantasia': $catClass = 'text-blue-400'; break;
                                        default: $catClass = 'text-gray-400';
                                    }
                                @endphp
                                <tr class="border-b last:border-0">
                                    <td class="py-2 px-2">{{ $shipment->id }}</td>
                                    <td class="py-2 px-2">{{ $shipment->description }}</td>
                                    <td class="py-2 px-2">{{ $shipment->height }}</td>
                                    <td class="py-2 px-2">{{ $shipment->width }}</td>
                                    <td class="py-2 px-2">
                                        <span class="{{ $styleClass }} font-semibold">
                                            {{ $shipment->style }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-2">
                                        <span class="{{ $catClass }} font-semibold">
                                            {{ $shipment->category }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-2">{{ $shipment->created_at->format('d/m/Y') }}</td>
                                    <td class="py-2 px-2">
                                        <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-blue-100 text-gray-700 px-3 py-1 rounded shadow hover:bg-pink-100 transition border border-blue-200"
                                                onclick="return confirm('¿Seguro que quieres eliminar este shipment?')">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-pink-400 py-4 text-lg">No hay shipments.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection