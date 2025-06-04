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
    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'height' => 'required|numeric',
        'width' => 'required|numeric',
        'style' => 'required|string',
        'category' => 'required|string',
        'imagen' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Guardar la imagen
    $imagePath = $request->file('imagen')->store('obras', 'public');
    $imageName = basename($imagePath);

    // Crear producto
    Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'height' => $request->height,
        'width' => $request->width,
        'style' => $request->style,
        'category' => $request->category,
        'imagen' => $imageName,
    ]);

    return redirect()->route('galeria')->with('success', 'Obra guardada correctamente');
}

}
