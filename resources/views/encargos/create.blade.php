@extends('layouts.app')
@use('App\Enums\Category')
@use('App\Enums\Style')

@section('title', "Solicitar presupuesto · Nando's Art")

@section('content')
<div class="max-w-2xl bg-white p-6 sm:p-8 rounded-sm shadow-sm my-10 mx-4 sm:mx-auto">
    <h2 class="text-2xl font-bold text-center mb-6">Solicitar Presupuesto</h2>

    <form action="{{ route('encargos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Descripción</label>
            <textarea name="description" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-md">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Altura (cm)</label>
                <input type="number" name="height" step="0.01" min="0" value="{{ old('height') }}" required class="w-full px-4 py-2 border rounded-md">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Ancho (cm)</label>
                <input type="number" name="width" step="0.01" min="0" value="{{ old('width') }}" required class="w-full px-4 py-2 border rounded-md">
            </div>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Estilo</label>
            <select name="style" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                @foreach (Style::cases() as $style)
                    <option value="{{ $style->value }}" @selected(old('style') === $style->value)>{{ $style->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Categoría</label>
            <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                @foreach (Category::cases() as $category)
                    <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Imagen (opcional)</label>
            <input type="file" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-gray-600">
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                Enviar Presupuesto
            </button>
        </div>
    </form>
</div>
@endsection
