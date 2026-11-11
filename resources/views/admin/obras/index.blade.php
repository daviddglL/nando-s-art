@extends('layouts.app')
@section('title', "Obras · Nando's Art")

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
        <h1 class="titulo-seccion">Obras</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primario">
            + Nueva obra
        </a>
    </div>

    <p class="text-tinta-suave mb-4">Este es el orden en que salen en la galería. Usa las flechas para cambiarlo. Las obras con ★ salen en la portada (las {{ \App\Models\Product::MAX_DESTACADAS }} primeras).</p>

    <div class="lamina p-0 sm:p-2 overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
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
                    <tr>
                        <td class="py-2 px-3 whitespace-nowrap">
                            @foreach (['arriba' => ['▲', 'Subir'], 'abajo' => ['▼', 'Bajar']] as $direccion => [$flecha, $accion])
                                <form method="POST" action="{{ route('admin.obras.mover', $obra) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direccion" value="{{ $direccion }}">
                                    <button type="submit" class="px-2 py-1 rounded-md hover:bg-papel-oscuro" aria-label="{{ $accion }} {{ $obra->name }}">{{ $flecha }}</button>
                                </form>
                            @endforeach
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $obra->miniaturaUrl() }}" alt="" loading="lazy" class="w-14 h-14 object-cover rounded-md">
                                <a href="{{ route('obras.show', $obra->slug) }}" class="font-semibold text-tinta hover:underline">{{ $obra->name }}</a>
                            </div>
                        </td>
                        <td class="py-2 px-3">{{ $obra->style->label() }}</td>
                        <td class="py-2 px-3 whitespace-nowrap">{{ $obra->precioTexto() }}</td>
                        <td class="py-2 px-3">
                            <span @class(['text-salvia' => $obra->available, 'text-tinta-tenue' => ! $obra->available])>
                                {{ $obra->available ? 'Disponible' : 'Vendida' }}
                            </span>
                            <form method="POST" action="{{ route('admin.obras.destacar', $obra) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" aria-pressed="{{ $obra->featured ? 'true' : 'false' }}"
                                        title="{{ $obra->featured ? 'Quitar de la portada' : 'Mostrar en la portada' }}"
                                        @class(['ml-1 px-2 py-0.5 rounded-sm hover:bg-acento-claro', 'text-acento' => $obra->featured, 'text-tinta-tenue' => ! $obra->featured])>
                                    {{ $obra->featured ? '★ Destacada' : '☆ Destacar' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-2 px-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.obras.edit', $obra) }}" class="btn btn-secundario btn-pequeno">Editar</a>
                            <form method="POST" action="{{ route('admin.obras.destroy', $obra) }}" class="inline"
                                  data-confirmar="¿Enviar esta obra a la papelera?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-peligro btn-pequeno">Borrar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-tinta-suave py-6">Todavía no hay obras.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $obras->links() }}</div>

    @if ($papelera->isNotEmpty())
        <h2 class="font-display text-2xl mt-12 mb-1">Papelera</h2>
        <p class="text-tinta-suave mb-3">Las obras se eliminan solas, con su imagen, a los {{ \App\Models\Product::DIAS_PAPELERA }} días.</p>
        <ul class="lamina p-0 sm:p-0 divide-y divide-linea">
            @foreach ($papelera as $obra)
                <li class="flex flex-wrap justify-between items-center gap-2 p-3">
                    <span class="text-tinta-suave">{{ $obra->name }} <span class="text-sm text-tinta-tenue">(borrada el {{ $obra->deleted_at->format('d/m/Y') }})</span></span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.obras.restore', $obra) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secundario btn-pequeno">Restaurar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.obras.force-destroy', $obra) }}"
                              data-confirmar="¿Eliminar «{{ $obra->name }}» para siempre? También se borra su imagen y no se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-peligro btn-pequeno">Eliminar definitivamente</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
