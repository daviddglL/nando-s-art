@use('App\Enums\Category')
@use('App\Enums\Style')
{{-- Campos comunes de crear y editar obra. $obra es null al crear. --}}
@php
    $clasesCampo = 'campo';
@endphp

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
    <label for="name" class="etiqueta">Nombre de la obra</label>
    <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $obra?->name) }}" class="{{ $clasesCampo }}">
</div>

<div>
    <label for="description" class="etiqueta">Descripción</label>
    <textarea id="description" name="description" required rows="4" class="{{ $clasesCampo }}">{{ old('description', $obra?->description) }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="height" class="etiqueta">Altura (cm)</label>
        <input id="height" type="number" step="0.01" min="0" name="height" required value="{{ old('height', $obra?->height) }}" class="{{ $clasesCampo }}">
    </div>
    <div>
        <label for="width" class="etiqueta">Ancho (cm)</label>
        <input id="width" type="number" step="0.01" min="0" name="width" required value="{{ old('width', $obra?->width) }}" class="{{ $clasesCampo }}">
    </div>
    <div>
        <label for="year" class="etiqueta">Año (opcional)</label>
        <input id="year" type="number" min="1900" max="{{ date('Y') + 1 }}" name="year" value="{{ old('year', $obra?->year) }}" class="{{ $clasesCampo }}">
    </div>
    <div>
        <label for="price" class="etiqueta">Precio en € (vacío = a consultar)</label>
        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $obra?->price) }}" class="{{ $clasesCampo }}">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="style" class="etiqueta">Estilo</label>
        <select id="style" name="style" required class="{{ $clasesCampo }}">
            @foreach (Style::cases() as $style)
                <option value="{{ $style->value }}" @selected(old('style', $obra?->style->value) === $style->value)>{{ $style->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="category" class="etiqueta">Categoría</label>
        <select id="category" name="category" required class="{{ $clasesCampo }}">
            @foreach (Category::cases() as $category)
                <option value="{{ $category->value }}" @selected(old('category', $obra?->category->value) === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex flex-wrap gap-6">
    <label class="inline-flex items-center gap-2 text-tinta-suave">
        <input type="checkbox" name="available" value="1" @checked(old('available', $obra?->available ?? true)) class="w-4 h-4">
        Disponible (si no, aparece como vendida)
    </label>
    <label class="inline-flex items-center gap-2 text-tinta-suave">
        <input type="checkbox" name="featured" value="1" @checked(old('featured', $obra?->featured ?? false)) class="w-4 h-4">
        Destacada
    </label>
</div>

<div>
    <label for="imagen" class="etiqueta">
        {{ $obra ? 'Cambiar imagen (opcional)' : 'Imagen' }}
    </label>
    @if ($obra)
        <img src="{{ $obra->miniaturaUrl() }}" alt="Imagen actual de {{ $obra->name }}" class="w-32 h-32 object-cover rounded-md mb-2">
    @endif
    <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp" @required(! $obra)
           class="campo-fichero">
</div>
