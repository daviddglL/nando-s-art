<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\Style;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GaleriaController extends Controller
{
    // Los filtros llegan en la URL (/galeria?estilo=oleo&categoria=retrato&q=gato) para poder compartirlos;
    // un valor que no existe se ignora y sale el filtro por defecto
    public function index(Request $request)
    {
        return view('galeria', [
            'estilo' => Style::tryFrom((string) $request->query('estilo')) ?? Style::Acuarela,
            'categoria' => Category::tryFrom((string) $request->query('categoria')),
            'busqueda' => Str::limit(trim((string) $request->query('q')), 100, ''),
        ]);
    }
}
