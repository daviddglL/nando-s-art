@extends('layouts.app')
@section('title', "Editar {$pagina->titulo} · Nando's Art")

@php
    $clasesCampo = 'campo';
@endphp

@section('content')
    <div class="max-w-2xl mx-auto my-14 lamina">
        <h1 class="font-display text-4xl text-center mb-8">Editar «{{ $pagina->titulo }}»</h1>

        <form method="POST" action="{{ route('admin.paginas.update', $pagina) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="aviso aviso-error" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="titulo" class="etiqueta">Título</label>
                <input id="titulo" type="text" name="titulo" required maxlength="100" value="{{ old('titulo', $pagina->titulo) }}" class="{{ $clasesCampo }}">
            </div>

            <div>
                <label for="contenido" class="etiqueta">Texto</label>
                <textarea id="contenido" name="contenido" required rows="10" maxlength="10000" aria-describedby="contenido-ayuda" class="{{ $clasesCampo }}">{{ old('contenido', $pagina->contenido) }}</textarea>
                <p id="contenido-ayuda" class="text-sm text-tinta-tenue mt-1">Deja una línea en blanco para separar párrafos.</p>
            </div>

            @if ($pagina->esContacto())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="etiqueta">Email (opcional)</label>
                        <input id="email" type="email" name="email" maxlength="255" value="{{ old('email', $pagina->email) }}" class="{{ $clasesCampo }}">
                    </div>
                    <div>
                        <label for="telefono" class="etiqueta">Teléfono (opcional)</label>
                        <input id="telefono" type="tel" name="telefono" maxlength="30" value="{{ old('telefono', $pagina->telefono) }}" class="{{ $clasesCampo }}">
                    </div>
                    <div>
                        <label for="instagram" class="etiqueta">Instagram (opcional)</label>
                        <input id="instagram" type="url" name="instagram" maxlength="255" placeholder="https://www.instagram.com/usuario/" value="{{ old('instagram', $pagina->instagram) }}" class="{{ $clasesCampo }}">
                    </div>
                    <div>
                        <label for="ubicacion" class="etiqueta">Ubicación (opcional)</label>
                        <input id="ubicacion" type="text" name="ubicacion" maxlength="255" placeholder="Ciudad o zona" value="{{ old('ubicacion', $pagina->ubicacion) }}" class="{{ $clasesCampo }}">
                    </div>
                </div>
            @else
                <div>
                    <label for="foto" class="etiqueta">Foto (opcional)</label>
                    <div class="flex items-center gap-4">
                        <img src="{{ $pagina->fotoUrl() ?? asset('images/nandofoto-352.webp') }}" alt="Foto actual" class="w-20 h-20 rounded-full object-cover shadow-sm">
                        <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="{{ $clasesCampo }}">
                    </div>
                    @if ($pagina->foto)
                        <label class="inline-flex items-center gap-2 mt-2 text-tinta-suave">
                            <input type="hidden" name="quitar_foto" value="0">
                            <input type="checkbox" name="quitar_foto" value="1" @checked(old('quitar_foto'))>
                            Quitar la foto y usar la de siempre
                        </label>
                    @endif
                </div>
            @endif

            <div class="flex justify-center gap-4">
                <a href="{{ route('admin.paginas.index') }}" class="btn btn-secundario">Cancelar</a>
                <button type="submit" class="btn btn-primario">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
