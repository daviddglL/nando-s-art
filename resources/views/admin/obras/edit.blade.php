@extends('layouts.app')
@section('title', "Editar {$obra->name} · Nando's Art")

@section('content')
    <div class="max-w-2xl mx-auto my-10 bg-white p-6 sm:p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Editar obra</h1>

        <form method="POST" action="{{ route('admin.obras.update', $obra) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.obras.campos', ['obra' => $obra])

            <div class="flex justify-center gap-4">
                <a href="{{ route('admin.obras.index') }}" class="py-2 px-6 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Imágenes adicionales (detalles, otros ángulos...): salen en la ficha junto a la principal --}}
    <section id="imagenes" class="max-w-2xl mx-auto mb-10 bg-white p-6 sm:p-8 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Más imágenes</h2>
        <p class="text-gray-600 mb-4">
            Salen en la ficha de la obra después de la principal, en este orden.
            Hasta {{ \App\Http\Requests\SubirImagenesObraRequest::MAXIMO_POR_OBRA }} por obra.
        </p>

        @if ($obra->imagenes->isNotEmpty())
            <ul class="divide-y mb-6">
                @foreach ($obra->imagenes as $imagen)
                    <li class="flex flex-wrap items-center gap-3 py-3">
                        <img src="{{ $imagen->miniaturaUrl() }}" alt="Imagen {{ $loop->iteration }} de {{ $obra->name }}" loading="lazy" class="w-20 h-20 object-cover rounded-sm">
                        <span class="text-gray-700">Imagen {{ $loop->iteration }}</span>

                        <div class="ml-auto flex flex-wrap items-center gap-1">
                            @foreach (['arriba' => ['▲', 'Subir'], 'abajo' => ['▼', 'Bajar']] as $direccion => [$flecha, $accion])
                                <form method="POST" action="{{ route('admin.obras.imagenes.mover', [$obra, $imagen]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direccion" value="{{ $direccion }}">
                                    <button type="submit" class="px-2 py-1 rounded-sm hover:bg-gray-100" aria-label="{{ $accion }} la imagen {{ $loop->parent->iteration }}">{{ $flecha }}</button>
                                </form>
                            @endforeach
                            <form method="POST" action="{{ route('admin.obras.imagenes.principal', [$obra, $imagen]) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-blue-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-blue-200">Usar como principal</button>
                            </form>
                            <form method="POST" action="{{ route('admin.obras.imagenes.destroy', [$obra, $imagen]) }}" class="inline"
                                  data-confirmar="¿Eliminar esta imagen? No se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-pink-100 text-gray-700 px-3 py-1 rounded-sm hover:bg-pink-200">Eliminar</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($errors->imagenes->any())
            <div class="bg-red-100 text-red-800 p-3 rounded-sm mb-4" role="alert">
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
                    <label for="imagenes" class="block text-gray-700 font-semibold mb-1">Añadir imágenes</label>
                    <input id="imagenes" type="file" name="imagenes[]" multiple required accept="image/jpeg,image/png,image/gif,image/webp"
                           class="w-full border border-gray-300 rounded-lg p-2">
                    <p class="text-sm text-gray-500 mt-1">Puedes elegir varias a la vez. JPG, PNG, GIF o WebP de hasta 2 MB cada una.</p>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm">Subir</button>
            </form>
        @endif
    </section>
@endsection
