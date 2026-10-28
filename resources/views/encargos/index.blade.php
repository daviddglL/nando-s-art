@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen py-8 px-2 md:px-8 bg-gray-50">
    <div class="flex flex-col md:flex-row gap-6 w-full h-full">
        <!-- Columna izquierda: Usuarios -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-2xl shadow-lg h-full flex flex-col">
                <div class="text-center py-4 rounded-t-2xl bg-linear-to-r from-blue-200 to-pink-200">
                    <h4 class="text-gray-700 text-xl font-semibold mb-0">Usuarios</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-center">
                        <thead>
                            <tr class="bg-blue-50 text-gray-700">
                                <th class="py-2 px-2">ID</th>
                                <th class="py-2 px-2">Nombre</th>
                                <th class="py-2 px-2">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="py-1 px-2">{{ $user->id }}</td>
                                    <td class="py-1 px-2">{{ $user->name }}</td>
                                    <td class="py-1 px-2">{{ $user->email }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-pink-400 py-4">No hay usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Columna derecha: encargos -->
        <div class="w-full md:w-2/3">
            <div class="flex justify-end mb-4">
                <a href="{{ route('encargos.create') }}"
                   class="bg-linear-to-r from-blue-200 to-pink-200 text-gray-700 px-5 py-2 rounded-lg shadow-sm font-semibold hover:scale-105 transition border border-blue-100">
                    + Nuevo presupuesto
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
                                <th class="py-3 px-2">Cliente</th>
                                <th class="py-3 px-2">Estado</th>
                                <th class="py-3 px-2">Fecha</th>
                                <th class="py-3 px-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($encargos as $encargo)
                                <tr class="border-b last:border-0">
                                    <td class="py-2 px-2">{{ $encargo->id }}</td>
                                    <td class="py-2 px-2">{{ $encargo->description }}</td>
                                    <td class="py-2 px-2">{{ $encargo->height }}</td>
                                    <td class="py-2 px-2">{{ $encargo->width }}</td>
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
                                    <td class="py-2 px-2">{{ $encargo->user?->name }}</td>
                                    <td class="py-2 px-2">{{ $encargo->status->label() }}</td>
                                    <td class="py-2 px-2">{{ $encargo->created_at->format('d/m/Y') }}</td>
                                    <td class="py-2 px-2">
                                        <form action="{{ route('encargos.destroy', $encargo->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-blue-100 text-gray-700 px-3 py-1 rounded-sm shadow-sm hover:bg-pink-100 transition border border-blue-200"
                                                onclick="return confirm('¿Seguro que quieres eliminar este presupuesto?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-pink-400 py-4 text-lg">No hay presupuestos.</td>
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