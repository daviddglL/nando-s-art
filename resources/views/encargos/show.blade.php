@extends('layouts.app')
@use('App\Enums\EstadoEncargo')
@section('title', "Presupuesto del {$encargo->created_at->format('d/m/Y')} · Nando's Art")

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    @can('viewAny', App\Models\Encargo::class)
        <a href="{{ route('encargos.index') }}" class="text-blue-600 hover:underline">← Presupuestos recibidos</a>
    @else
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">← Mis presupuestos</a>
    @endcan

    <div class="mt-4 bg-white rounded-2xl shadow-lg p-6 sm:p-8">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Presupuesto del {{ $encargo->created_at->format('d/m/Y') }}</h1>
                @can('viewAny', App\Models\Encargo::class)
                    <p class="text-gray-600">{{ $encargo->user?->name }} · <a href="mailto:{{ $encargo->user?->email }}" class="text-blue-600 hover:underline">{{ $encargo->user?->email }}</a></p>
                @endcan
            </div>
            <x-estado-encargo :estado="$encargo->status" />
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-700 mb-6">
            <div><dt class="font-semibold">Medidas</dt><dd>{{ $encargo->height }} x {{ $encargo->width }} cm</dd></div>
            <div><dt class="font-semibold">Estilo</dt><dd>{{ $encargo->styleEnum()?->label() ?? $encargo->style }}</dd></div>
            <div><dt class="font-semibold">Categoría</dt><dd>{{ $encargo->categoryEnum()?->label() ?? $encargo->category }}</dd></div>
        </dl>

        <h2 class="font-semibold text-gray-800 mb-1">Descripción</h2>
        <p class="text-gray-700 whitespace-pre-line mb-6">{{ $encargo->description }}</p>

        @if ($encargo->imagenUrl())
            <h2 class="font-semibold text-gray-800 mb-2">Imagen de referencia</h2>
            <a href="{{ $encargo->imagenUrl() }}" target="_blank" rel="noopener">
                <img src="{{ $encargo->imagenUrl() }}" alt="Imagen de referencia del presupuesto" class="max-h-80 rounded-lg shadow-sm mb-6">
            </a>
        @endif

        {{-- Respuesta del artista --}}
        <section class="border-t pt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-3">Respuesta</h2>

            @can('update', $encargo)
                <form method="POST" action="{{ route('encargos.update', $encargo) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    @if ($errors->any())
                        <div class="bg-red-100 text-red-700 p-3 rounded-sm" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-gray-700 font-semibold mb-1">Estado</label>
                            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                                @foreach (EstadoEncargo::cases() as $estado)
                                    <option value="{{ $estado->value }}" @selected(old('status', $encargo->status->value) === $estado->value)>{{ $estado->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="precio" class="block text-gray-700 font-semibold mb-1">Precio orientativo (€)</label>
                            <input id="precio" type="number" name="precio" step="0.01" min="0" value="{{ old('precio', $encargo->precio) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div>
                        <label for="respuesta" class="block text-gray-700 font-semibold mb-1">Mensaje para el cliente</label>
                        <textarea id="respuesta" name="respuesta" rows="5" maxlength="5000"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md">{{ old('respuesta', $encargo->respuesta) }}</textarea>
                    </div>

                    <p class="text-sm text-gray-600">Al guardar un cambio, el cliente recibe un email con el estado, el mensaje y el precio.</p>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm">
                        Guardar y avisar al cliente
                    </button>
                </form>
            @else
                @if ($encargo->respuesta || $encargo->precio !== null)
                    @if ($encargo->respuesta)
                        <p class="text-gray-700 whitespace-pre-line mb-3">{{ $encargo->respuesta }}</p>
                    @endif
                    <p class="text-gray-700"><span class="font-semibold">Precio orientativo:</span> {{ $encargo->precioTexto() }}</p>
                    @if ($encargo->respondido_at)
                        <p class="text-sm text-gray-500 mt-2">Respondido el {{ $encargo->respondido_at->format('d/m/Y') }}</p>
                    @endif
                @else
                    <p class="text-gray-600">Todavía no hay respuesta. Te avisaremos por email en cuanto la haya.</p>
                @endif
            @endcan
        </section>
    </div>
</div>
@endsection
