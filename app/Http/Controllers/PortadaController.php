<?php

namespace App\Http\Controllers;

use App\Models\Product;

// Portada (/): presentación del artista y las obras que el admin marca como destacadas
class PortadaController extends Controller
{
    public function __invoke()
    {
        $destacadas = Product::destacadas()->limit(Product::MAX_DESTACADAS)->get();

        return view('landing', compact('destacadas'));
    }
}
