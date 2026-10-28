<?php

namespace App\Http\Controllers;

use App\Models\Product;

// Ficha pública de una obra (/obras/{slug})
class ObraController extends Controller
{
    public function show(Product $obra)
    {
        // Otras obras del mismo estilo para seguir navegando
        $relacionadas = Product::query()
            ->where('style', $obra->style)
            ->whereKeyNot($obra->id)
            ->ordenadas()
            ->limit(4)
            ->get();

        return view('obras.show', compact('obra', 'relacionadas'));
    }
}
