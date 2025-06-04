@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Formulario de Nueva Obra</h1>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Nombre de la obra</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Descripción</label>
                <textarea name="description" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="block text-gray-700 font-semibold mb-1">Altura (cm)</label>
                    <input type="number" step="0.01" name="height" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-1/2">
                    <label class="block text-gray-700 font-semibold mb-1">Ancho (cm)</label>
                    <input type="number" step="0.01" name="width" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Estilo</label>
                <select name="style" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="acuarela">Acuarela</option>
                    <option value="rotulador">Rotulador</option>
                    <option value="oleo">Óleo</option>
                    <option value="sketch">Sketch</option>
                    <option value="papel">Papel</option>
                    <option value="lienzo">Lienzo</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Categoría</label>
                <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="retrato">Retrato</option>
                    <option value="paisaje">Paisaje</option>
                    <option value="abstracto">Abstracto</option>
                    <option value="urbano">Urbano</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Imagen</label>
                <input type="file" name="imagen" required class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
            </div>

            <div class="text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                    Guardar
                </button>
            </div>
        </form>
    </div>
@endsection
