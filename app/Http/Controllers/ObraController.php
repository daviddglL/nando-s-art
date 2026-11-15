<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\Style;
use App\Galeria\Catalogo;
use App\Galeria\Obra;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObraController extends Controller
{
    // Ficha pública de una obra (/obras/{slug})
    public function show(Catalogo $catalogo, string $slug)
    {
        $obra = $catalogo->buscar($slug) ?? abort(404);

        return view('obras.show', [
            'obra' => $obra,
            'relacionadas' => $catalogo->relacionadas($obra),
        ]);
    }

    // API de solo lectura que usa el carrusel: /api/products?style=acuarela&category=retrato&q=gato
    public function index(Request $request, Catalogo $catalogo): JsonResponse
    {
        $filtros = $request->validate([
            'style' => ['nullable', Rule::enum(Style::class)],
            'category' => ['nullable', Rule::enum(Category::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $obras = $catalogo->filtrar(
            Style::tryFrom((string) ($filtros['style'] ?? '')),
            Category::tryFrom((string) ($filtros['category'] ?? '')),
            (string) ($filtros['q'] ?? ''),
        );

        return response()->json(['data' => $obras->map(fn (Obra $obra) => $obra->paraApi())->all()]);
    }

    public function api(Catalogo $catalogo, string $slug): JsonResponse
    {
        $obra = $catalogo->buscar($slug) ?? abort(404);

        return response()->json(['data' => $obra->paraApi()]);
    }
}
