@extends('layouts.app')
@section('title', "Editar {$obra->name} · Nando's Art")

@section('content')
    <div class="max-w-2xl mx-auto my-14 lamina">
        <h1 class="font-display text-4xl text-center mb-8">Editar obra</h1>

        <form method="POST" action="{{ route('admin.obras.update', $obra) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.obras.campos', ['obra' => $obra])

            <div class="flex justify-center gap-4">
                <a href="{{ route('admin.obras.index') }}" class="btn btn-secundario">Cancelar</a>
                <button type="submit" class="btn btn-primario">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Imágenes adicionales (detalles, otros ángulos...): salen en la ficha junto a la principal --}}
    <section id="imagenes" class="max-w-2xl mx-auto mb-10 bg-papel-claro p-6 sm:p-8 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-tinta mb-1">Más imágenes</h2>
        <p class="text-tinta-suave mb-4">
            Salen en la ficha de la obra después de la principal, en este orden.
            Hasta {{ \App\Http\Requests\SubirImagenesObraRequest::MAXIMO_POR_OBRA }} por obra.
        </p>

        @if ($obra->imagenes->isNotEmpty())
            <ul class="divide-y mb-6">
                @foreach ($obra->imagenes as $imagen)
                    <li class="flex flex-wrap items-center gap-3 py-3">
                        <img src="{{ $imagen->miniaturaUrl() }}" alt="Imagen {{ $loop->iteration }} de {{ $obra->name }}" loading="lazy" class="w-20 h-20 object-cover rounded-md">
                        <span class="text-tinta-suave">Imagen {{ $loop->iteration }}</span>

                        <div class="ml-auto flex flex-wrap items-center gap-1">
                            @foreach (['arriba' => ['▲', 'Subir'], 'abajo' => ['▼', 'Bajar']] as $direccion => [$flecha, $accion])
                                <form method="POST" action="{{ route('admin.obras.imagenes.mover', [$obra, $imagen]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direccion" value="{{ $direccion }}">
                                    <button type="submit" class="px-2 py-1 rounded-md hover:bg-papel-oscuro" aria-label="{{ $accion }} la imagen {{ $loop->parent->iteration }}">{{ $flecha }}</button>
                                </form>
                            @endforeach
                            <form method="POST" action="{{ route('admin.obras.imagenes.principal', [$obra, $imagen]) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secundario btn-pequeno">Usar como principal</button>
                            </form>
                            <form method="POST" action="{{ route('admin.obras.imagenes.destroy', [$obra, $imagen]) }}" class="inline"
                                  data-confirmar="¿Eliminar esta imagen? No se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-peligro btn-pequeno">Eliminar</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($errors->imagenes->any())
            <div class="bg-red-100 text-red-800 p-3 rounded-md mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->imagenes->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($obra->imagenes->count() < \App\Http\Requests\SubirImagenesObraRequest::MAXIMO_POR_OBRA)
            <form method="POST" action="{{ route('admin.obras.imagenes.store', $obra) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
                @csrf
                <div class="grow">
                    <label for="imagenes" class="etiqueta">Añadir imágenes</label>
                    <input id="imagenes" type="file" name="imagenes[]" multiple required accept="image/jpeg,image/png,image/gif,image/webp"
                           class="campo">
                    <p class="text-sm text-tinta-tenue mt-1">Puedes elegir varias a la vez. JPG, PNG, GIF o WebP de hasta 2 MB cada una.</p>
                </div>
                <button type="submit" class="btn btn-primario">Subir</button>
            </form>
        @endif
    </section>
@endsection
