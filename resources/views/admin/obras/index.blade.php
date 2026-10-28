@extends('layouts.app')
@section('title', "Obras · Nando's Art")

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Obras</h1>
        <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
            + Nueva obra
        </a>
    </div>

    <p class="text-gray-600 mb-4">Este es el orden en que salen en la galería. Usa las flechas para cambiarlo.</p>

    <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-blue-50 text-gray-700">
                    <th class="py-3 px-3">Orden</th>
                    <th class="py-3 px-3">Obra</th>
                    <th class="py-3 px-3">Estilo</th>
                    <th class="py-3 px-3">Precio</th>
                    <th class="py-3 px-3">Estado</th>
                    <th class="py-3 px-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($obras as $obra)
                    <tr class="border-b last:border-0 align-middle">
                        <td class="py-2 px-3 whitespace-nowrap">
                            @foreach (['arriba' => ['▲', 'Subir'], 'abajo' => ['▼', 'Bajar']] as $direccion => [$flecha, $accion])
                                <form method="POST" action="{{ route('admin.obras.mover', $obra) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direccion" value="{{ $direccion }}">
                                    <button type="submit" class="px-2 py-1 rounded-sm hover:bg-gray-100" aria-label="{{ $accion }} {{ $obra->name }}">{{ $flecha }}</button>
                                </form>
                            @endforeach
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $obra->imagenUrl() }}" alt="" loading="lazy" class="w-14 h-14 object-cover rounded-sm">
                                <a href="{{ route('obras.show', $obra->slug) }}" class="font-semibold text-gray-800 hover:underline">{{ $obra->name }}</a>
                            </div>
                        </td>
                        <td class="py-2 px-3">{{ $obra->style->label() }}</td>
                        <td class="py-2 px-3 whitespace-nowrap">{{ $obra->precioTexto() }}</td>
                        <td class="py-2 px-3">
                            <span @class(['text-green-700' => $obra->available, 'text-gray-500' => ! $obra->available])>
                                {{ $obra->available ? 'Disponible' : 'Vendida' }}
                            </span>
                            @if ($obra->featured)
                                <span class="ml-1 text-yellow-700">★ Destacada</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.obras.edit', $obra) }}" class="inline-block bg-blue-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-blue-200">Editar</a>
                            <form method="POST" action="{{ route('admin.obras.destroy', $obra) }}" class="inline"
                                  onsubmit="return confirm('¿Enviar esta obra a la papelera?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-pink-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-pink-200">Borrar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-600 py-6">Todavía no hay obras.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $obras->links() }}</div>

    @if ($papelera->isNotEmpty())
        <h2 class="text-xl font-bold text-gray-800 mt-10 mb-3">Papelera</h2>
        <ul class="bg-white rounded-2xl shadow-lg divide-y">
            @foreach ($papelera as $obra)
                <li class="flex flex-wrap justify-between items-center gap-2 p-3">
                    <span class="text-gray-700">{{ $obra->name }} <span class="text-sm text-gray-500">(borrada el {{ $obra->deleted_at->format('d/m/Y') }})</span></span>
                    <form method="POST" action="{{ route('admin.obras.restore', $obra) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bg-green-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-green-200">Restaurar</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
