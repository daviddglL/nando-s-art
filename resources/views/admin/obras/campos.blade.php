@use('App\Enums\Category')
@use('App\Enums\Style')
{{-- Campos comunes de crear y editar obra. $obra es null al crear. --}}
@php
    $clasesCampo = 'w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-hidden focus:ring-2 focus:ring-blue-500';
@endphp

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded-sm" role="alert">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div>
    <label for="name" class="block text-gray-700 font-semibold mb-1">Nombre de la obra</label>
    <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $obra?->name) }}" class="{{ $clasesCampo }}">
</div>

<div>
    <label for="description" class="block text-gray-700 font-semibold mb-1">Descripción</label>
    <textarea id="description" name="description" required rows="4" class="{{ $clasesCampo }}">{{ old('description', $obra?->description) }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="height" class="block text-gray-700 font-semibold mb-1">Altura (cm)</label>
        <input id="height" type="number" step="0.01" min="0" name="height" required value="{{ old('height', $obra?->height) }}" class="{{ $clasesCampo }}">
    </div>
    <div>
        <label for="width" class="block text-gray-700 font-semibold mb-1">Ancho (cm)</label>
        <input id="width" type="number" step="0.01" min="0" name="width" required value="{{ old('width', $obra?->width) }}" class="{{ $clasesCampo }}">
    </div>
    <div>
        <label for="year" class="block text-gray-700 font-semibold mb-1">Año (opcional)</label>
        <input id="year" type="number" min="1900" max="{{ date('Y') + 1 }}" name="year" value="{{ old('year', $obra?->year) }}" class="{{ $clasesCampo }}">
    </div>
    <div>
        <label for="price" class="block text-gray-700 font-semibold mb-1">Precio en € (vacío = a consultar)</label>
        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $obra?->price) }}" class="{{ $clasesCampo }}">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="style" class="block text-gray-700 font-semibold mb-1">Estilo</label>
        <select id="style" name="style" required class="{{ $clasesCampo }}">
            @foreach (Style::cases() as $style)
                <option value="{{ $style->value }}" @selected(old('style', $obra?->style->value) === $style->value)>{{ $style->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="category" class="block text-gray-700 font-semibold mb-1">Categoría</label>
        <select id="category" name="category" required class="{{ $clasesCampo }}">
            @foreach (Category::cases() as $category)
                <option value="{{ $category->value }}" @selected(old('category', $obra?->category->value) === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex flex-wrap gap-6">
    <label class="inline-flex items-center gap-2 text-gray-700">
        <input type="checkbox" name="available" value="1" @checked(old('available', $obra?->available ?? true)) class="w-4 h-4">
        Disponible (si no, aparece como vendida)
    </label>
    <label class="inline-flex items-center gap-2 text-gray-700">
        <input type="checkbox" name="featured" value="1" @checked(old('featured', $obra?->featured ?? false)) class="w-4 h-4">
        Destacada
    </label>
</div>

<div>
    <label for="imagen" class="block text-gray-700 font-semibold mb-1">
        {{ $obra ? 'Cambiar imagen (opcional)' : 'Imagen' }}
    </label>
    @if ($obra)
        <img src="{{ $obra->miniaturaUrl() }}" alt="Imagen actual de {{ $obra->name }}" class="w-32 h-32 object-cover rounded-sm mb-2">
    @endif
    <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp" @required(! $obra)
           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
</div>
