<?php

namespace App\Http\Controllers;

use App\Galeria\Catalogo;
use App\Galeria\Pagina;
use Illuminate\Http\Request;

// Páginas de texto (resources/contenido/paginas.php)
class PaginaController extends Controller
{
    public function sobreMi()
    {
        return view('paginas.sobre-mi', ['pagina' => Pagina::leer(Pagina::SOBRE_MI)]);
    }

    // Desde la ficha de una obra se llega con ?obra=slug y el mensaje sale ya empezado
    public function contacto(Request $request, Catalogo $catalogo)
    {
        $obra = $catalogo->buscar((string) $request->query('obra'));

        return view('paginas.contacto', [
            'pagina' => Pagina::leer(Pagina::CONTACTO),
            'mensajeInicial' => $obra ? "Hola, me interesa la obra «{$obra->name}». " : '',
        ]);
    }
}
