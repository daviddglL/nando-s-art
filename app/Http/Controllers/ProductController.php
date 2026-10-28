<?php

namespace App\Http\Controllers;

use App\Actions\CreateProduct;
use App\Enums\Style;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // El carrusel de la galería pide /api/products?style=acuarela
        $request->validate(['style' => ['nullable', Rule::enum(Style::class)]]);

        $products = Product::query()
            ->when($request->query('style'), fn ($query, $style) => $query->where('style', $style))
            ->get();

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        return view('admin.create');
    }

    public function store(StoreProductRequest $request, CreateProduct $createProduct)
    {
        $createProduct->handle($request->safe()->except('imagen'), $request->file('imagen'));

        return redirect()->route('galeria')->with('success', 'Obra creada correctamente');
    }
}
