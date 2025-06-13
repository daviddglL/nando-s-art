<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }
    public function create()
{
    return view('admin.create'); // ← asegúrate de tener esta vista
}




public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'height' => 'required|numeric',
        'width' => 'required|numeric',
        'style' => 'required|string',
        'category' => 'required|string',
        'imagen' => 'required|image|max:2048',
    ]);

    if ($request->hasFile('imagen')) {
        $file = $request->file('imagen');
        $nombreImagen = $file->getClientOriginalName();

        // Ruta absoluta donde guardar la imagen:
        $rutaFisica = public_path('storage/obras/' . $nombreImagen);

        if (file_exists($rutaFisica)) {
            // Si ya existe, solo guardamos la ruta relativa
            $validated['imagen'] =  $nombreImagen;
        } else {
            // Movemos el archivo a la ruta física deseada
            $file->move(public_path('storage/obras'), $nombreImagen);
            $validated['imagen'] =  $nombreImagen;
        }
    }

    Product::create($validated);

    return redirect()->route('galeria')->with('success', 'Obra creada correctamente');
}


}
