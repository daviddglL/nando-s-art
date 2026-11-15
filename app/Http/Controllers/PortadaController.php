<?php

namespace App\Http\Controllers;

use App\Galeria\Catalogo;
use App\Galeria\Pagina;

// Portada (/): presentación del artista (de su página «Sobre mí») y las obras marcadas como destacadas
class PortadaController extends Controller
{
    public function __invoke(Catalogo $catalogo)
    {
        $sobreMi = Pagina::leer(Pagina::SOBRE_MI);

        return view('landing', [
            'destacadas' => $catalogo->destacadas(),
            'sobreMi' => $sobreMi,
            'contacto' => Pagina::leer(Pagina::CONTACTO),
            // El primer párrafo de «Sobre mí»: si cambia la biografía, la portada cambia con ella
            'presentacion' => $sobreMi->primerParrafo(),
        ]);
    }
}
