<?php

namespace App\Http\Controllers;

use App\Actions\SolicitarPresupuesto;
use App\Http\Requests\StoreEncargoRequest;
use App\Models\Encargo;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class EncargoController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Encargo::class);

        $encargos = Encargo::with('user')->latest()->get();
        $users = User::all();

        return view('encargos.index', compact('encargos', 'users'));
    }

    public function create()
    {
        Gate::authorize('create', Encargo::class);

        return view('encargos.create');
    }

    public function store(StoreEncargoRequest $request, SolicitarPresupuesto $solicitar)
    {
        $solicitar->handle($request->user(), $request->safe()->except('imagen'), $request->file('imagen'));

        $ruta = $request->user()->isAdmin() ? 'encargos.index' : 'dashboard';

        return redirect()->route($ruta)->with('success', 'Presupuesto enviado correctamente');
    }

    public function destroy(Encargo $encargo)
    {
        Gate::authorize('delete', $encargo);

        $encargo->delete();

        return redirect()->route('encargos.index')->with('success', 'Presupuesto eliminado correctamente');
    }
}
