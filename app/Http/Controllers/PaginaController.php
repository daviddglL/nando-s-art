<?php

namespace App\Http\Controllers;

use App\Actions\ActualizarPagina;
use App\Http\Requests\UpdatePaginaRequest;
use App\Models\Pagina;
use Illuminate\Support\Facades\Gate;

class PaginaController extends Controller
{
    // --- Páginas públicas ---

    public function sobreMi()
    {
        return view('paginas.sobre-mi', ['pagina' => Pagina::obtener(Pagina::SOBRE_MI)]);
    }

    public function contacto()
    {
        return view('paginas.contacto', ['pagina' => Pagina::obtener(Pagina::CONTACTO)]);
    }

    // --- Panel del admin ---

    public function index()
    {
        Gate::authorize('viewAny', Pagina::class);

        return view('admin.paginas.index', ['paginas' => Pagina::todas()]);
    }

    public function edit(Pagina $pagina)
    {
        Gate::authorize('update', $pagina);

        return view('admin.paginas.edit', compact('pagina'));
    }

    public function update(UpdatePaginaRequest $request, Pagina $pagina, ActualizarPagina $actualizar)
    {
        $actualizar->handle($pagina, $request->validated(), $request->file('foto'), $request->boolean('quitar_foto'));

        return redirect()->route('admin.paginas.index')->with('success', "Página «{$pagina->titulo}» guardada.");
    }
}
