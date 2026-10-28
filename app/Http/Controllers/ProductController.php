<?php

namespace App\Http\Controllers;

use App\Actions\CreateProduct;
use App\Actions\MoverObra;
use App\Actions\UpdateProduct;
use App\Enums\Category;
use App\Enums\Style;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // --- API pública (la usa la galería) ---

    public function index(Request $request): AnonymousResourceCollection
    {
        // El carrusel de la galería pide /api/products?style=acuarela&category=retrato&q=gato
        $filtros = $request->validate([
            'style' => ['nullable', Rule::enum(Style::class)],
            'category' => ['nullable', Rule::enum(Category::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $consulta = Product::query()
            ->when($filtros['style'] ?? null, fn ($query, $style) => $query->where('style', $style))
            ->when($filtros['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->ordenadas();

        // Las búsquedas de texto no se cachean: cada texto distinto llenaría la caché
        if ($busqueda = trim($filtros['q'] ?? '')) {
            $patron = '%'.$busqueda.'%';

            return ProductResource::collection($consulta
                ->where(fn ($query) => $query->whereLike('name', $patron)->orWhereLike('description', $patron))
                ->get());
        }

        // Se cachea por filtro; al crear, editar o borrar una obra cambia la versión y la caché se descarta sola
        $version = Cache::rememberForever(Product::VERSION_CACHE, fn () => 'v1');
        $clave = 'obras.api.'.$version.'.'.($filtros['style'] ?? '').'.'.($filtros['category'] ?? '');

        $products = Cache::remember($clave, now()->addDay(), fn () => $consulta->get());

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    // --- Panel del admin ---

    public function listado()
    {
        Gate::authorize('viewAny', Product::class);

        $obras = Product::ordenadas()->paginate(20);
        $papelera = Product::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.obras.index', compact('obras', 'papelera'));
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

    public function edit(Product $obra)
    {
        Gate::authorize('update', $obra);

        return view('admin.obras.edit', compact('obra'));
    }

    public function update(UpdateProductRequest $request, Product $obra, UpdateProduct $updateProduct)
    {
        $updateProduct->handle($obra, $request->safe()->except('imagen'), $request->file('imagen'));

        return redirect()->route('admin.obras.index')->with('success', 'Obra actualizada correctamente');
    }

    // Borrado suave: la obra desaparece de la galería pero se puede restaurar desde la papelera
    public function destroy(Product $obra)
    {
        Gate::authorize('delete', $obra);

        $obra->delete();

        return redirect()->route('admin.obras.index')->with('success', 'Obra enviada a la papelera');
    }

    public function restore(Product $obra)
    {
        Gate::authorize('restore', $obra);

        $obra->restore();

        return redirect()->route('admin.obras.index')->with('success', 'Obra restaurada');
    }

    // Borrado definitivo: solo desde la papelera; se lleva también la imagen y sus miniaturas
    public function forceDestroy(Product $obra)
    {
        Gate::authorize('forceDelete', $obra);
        abort_unless($obra->trashed(), 404);

        $obra->forceDelete();

        return redirect()->route('admin.obras.index')->with('success', 'Obra eliminada definitivamente');
    }

    public function mover(Request $request, Product $obra, MoverObra $mover)
    {
        Gate::authorize('update', $obra);

        $direccion = $request->validate(['direccion' => 'required|in:arriba,abajo'])['direccion'];
        $mover->handle($obra, $direccion);

        return redirect()->route('admin.obras.index');
    }
}
