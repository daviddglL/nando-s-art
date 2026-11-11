@extends('layouts.app')
@use('App\Enums\Category')
@use('App\Enums\Style')

@section('title', "Solicitar presupuesto · Nando's Art")

@section('content')
<div class="max-w-2xl lamina my-14 mx-5 sm:mx-auto">
    <h1 class="font-display text-4xl text-center mb-8">Solicitar Presupuesto</h1>

    <form action="{{ route('encargos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="aviso aviso-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="encargo-description" class="etiqueta">Descripción</label>
            <textarea id="encargo-description" name="description" rows="4" required class="campo">{{ old('description', $descripcion) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="encargo-height" class="etiqueta">Altura (cm)</label>
                <input type="number" id="encargo-height" name="height" step="0.01" min="0" value="{{ old('height') }}" required class="campo">
            </div>
            <div>
                <label for="encargo-width" class="etiqueta">Ancho (cm)</label>
                <input type="number" id="encargo-width" name="width" step="0.01" min="0" value="{{ old('width') }}" required class="campo">
            </div>
        </div>

        <div>
            <label for="encargo-style" class="etiqueta">Estilo</label>
            <select id="encargo-style" name="style" required class="campo">
                @foreach (Style::cases() as $style)
                    <option value="{{ $style->value }}" @selected(old('style', $estilo) === $style->value)>{{ $style->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="encargo-category" class="etiqueta">Categoría</label>
            <select id="encargo-category" name="category" required class="campo">
                @foreach (Category::cases() as $category)
                    <option value="{{ $category->value }}" @selected(old('category', $categoria) === $category->value)>{{ $category->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="encargo-imagen" class="etiqueta">Imagen (opcional)</label>
            <input type="file" id="encargo-imagen" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp" class="campo-fichero">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primario">
                Enviar Presupuesto
            </button>
        </div>
    </form>
</div>
@endsection
