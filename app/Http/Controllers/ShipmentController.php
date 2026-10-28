<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index()
    {
        // Puedes ajustar la consulta según permisos o filtros
        $shipments = Shipment::all();
        $users = \App\Models\User::all();
        return view('shipments.index', compact('shipments', 'users'));
    }

    public function create()
    {
        return view('shipments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'style' => 'required|string',
            'category' => 'required|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('presupuestos', 'public');
        }

        // El presupuesto siempre pertenece al usuario autenticado
        $request->user()->shipments()->create($validated + ['status' => 'pending']);

        $ruta = $request->user()->role === 'admin' ? 'shipments.index' : 'dashboard';

        return redirect()->route($ruta)->with('success', 'Presupuesto enviado correctamente');
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', 'Shipment eliminado correctamente');
    }
}
