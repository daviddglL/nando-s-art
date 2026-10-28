<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\Style;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // El carrusel de la galería pide /api/products?style=acuarela
        return Product::query()
            ->when($request->query('style'), fn ($query, $style) => $query->where('style', $style))
            ->get();
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'style' => ['required', Rule::enum(Style::class)],
            'category' => ['required', Rule::enum(Category::class)],
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Nombre aleatorio: el cliente no decide el nombre y dos obras nunca comparten imagen.
        // Se guarda en storage/app/public/obras (visible en /storage/obras con `php artisan storage:link`)
        $validated['imagen'] = basename($request->file('imagen')->store('obras', 'public'));

        Product::create($validated);

        return redirect()->route('galeria')->with('success', 'Obra creada correctamente');
    }
}
