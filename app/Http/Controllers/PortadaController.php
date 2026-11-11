<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use App\Models\Product;
use Illuminate\Support\Str;

// Portada (/): presentación del artista (de su página «Sobre mí») y las obras que el admin marca como destacadas
class PortadaController extends Controller
{
    public function __invoke()
    {
        $destacadas = Product::destacadas()->limit(Product::MAX_DESTACADAS)->get();
        $sobreMi = Pagina::leer(Pagina::SOBRE_MI);

        return view('landing', [
            'destacadas' => $destacadas,
            'sobreMi' => $sobreMi,
            'contacto' => Pagina::leer(Pagina::CONTACTO),
            // El primer párrafo de «Sobre mí»: si el artista cambia su biografía, la portada cambia con ella
            'presentacion' => Str::limit(Str::before(trim((string) $sobreMi->contenido)."\n\n", "\n\n"), 420),
        ]);
    }
}
