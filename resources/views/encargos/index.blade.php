@extends('layouts.app')
@use('App\Enums\EstadoEncargo')
@section('title', "Presupuestos recibidos · Nando's Art")

@section('content')
<div class="contenedor py-12">
    <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
        <h1 class="titulo-seccion">Presupuestos recibidos</h1>
        <a href="{{ route('encargos.create') }}"
           class="btn btn-primario btn-pequeno">
            + Nuevo presupuesto
        </a>
    </div>

    {{-- Filtro por estado --}}
    <nav class="flex flex-wrap gap-2 mb-4" aria-label="Filtrar por estado">
        <a href="{{ route('encargos.index') }}" @class(['chip border border-tinta/15 text-sm', 'bg-tinta text-papel-claro' => ! $estado, 'bg-papel-claro text-tinta-suave hover:border-tinta' => $estado])
           @if (! $estado) aria-current="page" @endif>Todos</a>
        @foreach (EstadoEncargo::cases() as $opcion)
            <a href="{{ route('encargos.index', ['estado' => $opcion->value]) }}"
               @class(['chip border border-tinta/15 text-sm', 'bg-tinta text-papel-claro' => $estado === $opcion->value, 'bg-papel-claro text-tinta-suave hover:border-tinta' => $estado !== $opcion->value])
               @if ($estado === $opcion->value) aria-current="page" @endif>{{ $opcion->label() }}</a>
        @endforeach
    </nav>

    <div class="lamina p-2 sm:p-4 overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
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
                    <tr>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $encargo->created_at->format('d/m/Y') }}</td>
                        <td class="py-2 px-2">
                            {{ $encargo->user?->name }}
                            <span class="block text-sm text-tinta-tenue">{{ $encargo->user?->email }}</span>
                        </td>
                        <td class="py-2 px-2">{{ Str::limit($encargo->description, 80) }}</td>
                        <td class="py-2 px-2 whitespace-nowrap">{{ $encargo->height }} x {{ $encargo->width }}</td>
                        <td class="py-2 px-2">
                            <span class="{{ $encargo->styleEnum()?->textColor() ?? 'text-tinta-suave' }} font-semibold">
                                {{ $encargo->styleEnum()?->label() ?? $encargo->style }}
                            </span>
                        </td>
                        <td class="py-2 px-2">
                            <span class="{{ $encargo->categoryEnum()?->textColor() ?? 'text-tinta-tenue' }} font-semibold">
                                {{ $encargo->categoryEnum()?->label() ?? $encargo->category }}
                            </span>
                        </td>
                        <td class="py-2 px-2"><x-estado-encargo :estado="$encargo->status" /></td>
                        <td class="py-2 px-2 text-right whitespace-nowrap">
                            <a href="{{ route('encargos.show', $encargo) }}"
                               class="btn btn-secundario btn-pequeno">Responder</a>
                            <form action="{{ route('encargos.destroy', $encargo->id) }}" method="POST" class="inline"
                                  data-confirmar="¿Seguro que quieres eliminar este presupuesto?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-peligro btn-pequeno">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-tinta-suave py-4 text-lg">No hay presupuestos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $encargos->links() }}</div>
</div>
@endsection
