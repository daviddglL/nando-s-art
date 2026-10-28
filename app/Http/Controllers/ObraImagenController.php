<?php

namespace App\Http\Controllers;

use App\Actions\MoverImagenObra;
use App\Actions\SubirImagenesObra;
use App\Actions\UsarImagenComoPrincipal;
use App\Http\Requests\SubirImagenesObraRequest;
use App\Models\ObraImagen;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;

// Imágenes adicionales de una obra, desde su pantalla de edición en el panel del admin
class ObraImagenController extends Controller
{
    public function store(SubirImagenesObraRequest $request, Product $obra, SubirImagenesObra $subir)
    {
        /** @var list<UploadedFile> $ficheros */
        $ficheros = $request->file('imagenes');
        $subidas = count($subir->handle($obra, $ficheros));

        return $this->volver($obra, $subidas === 1 ? 'Imagen añadida' : "{$subidas} imágenes añadidas");
    }

    public function mover(Request $request, Product $obra, ObraImagen $imagen, MoverImagenObra $mover)
    {
        $this->autorizar($obra, $imagen);

        $direccion = $request->validate(['direccion' => 'required|in:arriba,abajo'])['direccion'];
        $mover->handle($imagen, $direccion);

        return $this->volver($obra);
    }

    public function principal(Product $obra, ObraImagen $imagen, UsarImagenComoPrincipal $usar)
    {
        $this->autorizar($obra, $imagen);

        $usar->handle($imagen);

        return $this->volver($obra, 'Imagen principal cambiada');
    }

    // Se borra al momento, con su fichero y sus miniaturas (no pasa por la papelera)
    public function destroy(Product $obra, ObraImagen $imagen)
    {
        $this->autorizar($obra, $imagen);

        $imagen->delete();

        return $this->volver($obra, 'Imagen eliminada');
    }

    // La imagen tiene que ser de la obra de la URL: así nadie toca imágenes de otra obra cambiando el id
    private function autorizar(Product $obra, ObraImagen $imagen): void
    {
        Gate::authorize('update', $obra);
        abort_unless($imagen->product_id === $obra->id, 404);
    }

    private function volver(Product $obra, ?string $mensaje = null)
    {
        $respuesta = redirect()->to(route('admin.obras.edit', $obra).'#imagenes');

        return $mensaje ? $respuesta->with('success', $mensaje) : $respuesta;
    }
}
