<h1>Formulario de nueva obra</h1>

<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="text" name="name" placeholder="Nombre de la obra" required>
    <textarea name="description" placeholder="Descripción" required></textarea>
    <input type="number" step="0.01" name="height" placeholder="Altura" required>
    <input type="number" step="0.01" name="width" placeholder="Ancho" required>
    
    <select name="style" required>
        <option value="acuarela">Acuarela</option>
        <option value="rotulador">Rotulador</option>
        <option value="oleo">Óleo</option>
        <option value="sketch">Sketch</option>
        <option value="papel">Papel</option>
        <option value="lienzo">Lienzo</option>
    </select>

    <select name="category" required>
        <option value="retrato">Retrato</option>
        <option value="paisaje">Paisaje</option>
        <option value="abstracto">Abstracto</option>
        <option value="urbano">Urbano</option>
        <option value="otro">Otro</option>
    </select>

    <input type="file" name="imagen" required>

    <button type="submit">Guardar</button>
</form>
