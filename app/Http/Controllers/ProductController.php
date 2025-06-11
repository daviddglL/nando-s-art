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

    // Guardar la imagen en public/images
    if ($request->hasFile('imagen')) {
        $imageName = time() . '_' . $request->file('imagen')->getClientOriginalName();
        $request->file('imagen')->storeAs('obras', $imageName, 'public');
        $validated['imagen'] = $imageName;
    }

    // Guardar el producto en la base de datos
    Product::create($validated);

    return redirect()->route('galeria')->with('success', 'Obra creada correctamente');
}

}
