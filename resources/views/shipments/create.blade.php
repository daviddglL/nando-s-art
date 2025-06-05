@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow mt-10">
    <h2 class="text-2xl font-bold text-center mb-6">Solicitar Presupuesto</h2>

    <form action="{{ route('shipments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Descripción</label>
            <textarea name="description" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-md"></textarea>
        </div>

        <div class="flex space-x-4">
            <div class="w-1/2">
                <label class="block text-gray-700 font-semibold mb-1">Altura (cm)</label>
                <input type="number" name="height" step="0.01" required class="w-full px-4 py-2 border rounded-md">
            </div>
            <div class="w-1/2">
                <label class="block text-gray-700 font-semibold mb-1">Ancho (cm)</label>
                <input type="number" name="width" step="0.01" required class="w-full px-4 py-2 border rounded-md">
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
            <label class="block text-gray-700 font-semibold mb-1">Imagen (opcional)</label>
            <input type="file" name="imagen" class="block w-full text-sm text-gray-600">
        </div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                Enviar Presupuesto
            </button>
        </div>
    </form>
</div>
@endsection
