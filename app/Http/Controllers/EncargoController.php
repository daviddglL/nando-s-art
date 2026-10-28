<?php

namespace App\Http\Controllers;

use App\Actions\ResponderEncargo;
use App\Actions\SolicitarPresupuesto;
use App\Enums\EstadoEncargo;
use App\Http\Requests\StoreEncargoRequest;
use App\Http\Requests\UpdateEncargoRequest;
use App\Models\Encargo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EncargoController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Encargo::class);

        // ?estado=pending filtra la lista; sin filtro salen todos, los más nuevos primero
        $estado = $request->validate(['estado' => ['nullable', Rule::enum(EstadoEncargo::class)]])['estado'] ?? null;

        $encargos = Encargo::with('user')
            ->when($estado, fn ($query) => $query->where('status', $estado))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('encargos.index', compact('encargos', 'estado'));
    }

    public function show(Encargo $encargo)
    {
        Gate::authorize('view', $encargo);

        $encargo->load('user');

        return view('encargos.show', compact('encargo'));
    }

    // Imagen de referencia: privada, solo para el cliente que pidió el presupuesto y para el admin
    public function imagen(Encargo $encargo): StreamedResponse
    {
        Gate::authorize('view', $encargo);

        $disco = Storage::disk(Encargo::DISCO_IMAGENES);
        abort_unless($encargo->imagen && $disco->exists($encargo->imagen), 404);

        return $disco->response($encargo->imagen, null, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Encargo::class);

        // Desde la ficha de una obra (/presupuesto?obra=slug) el formulario sale con su estilo y su tema
        $obra = Product::where('slug', (string) $request->query('obra'))->first();

        return view('encargos.create', [
            'estilo' => $obra?->style->value,
            'categoria' => $obra?->category->value,
            'descripcion' => $obra ? "Me gustaría una obra parecida a «{$obra->name}»." : null,
        ]);
    }

    public function store(StoreEncargoRequest $request, SolicitarPresupuesto $solicitar)
    {
        $solicitar->handle($request->user(), $request->safe()->except('imagen'), $request->file('imagen'));

        $ruta = $request->user()->isAdmin() ? 'encargos.index' : 'dashboard';

        return redirect()->route($ruta)->with('success', 'Presupuesto enviado correctamente');
    }

    public function update(UpdateEncargoRequest $request, Encargo $encargo, ResponderEncargo $responder)
    {
        $responder->handle($encargo, $request->validated());

        return redirect()->route('encargos.show', $encargo)->with('success', 'Presupuesto actualizado; el cliente recibirá un email');
    }

    public function destroy(Encargo $encargo)
    {
        Gate::authorize('delete', $encargo);

        $encargo->delete();

        return redirect()->route('encargos.index')->with('success', 'Presupuesto eliminado correctamente');
    }
}
