<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\ShipmentStatus;
use App\Enums\Style;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::with('user')->latest()->get();
        $users = User::all();

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
            'style' => ['required', Rule::enum(Style::class)],
            'category' => ['required', Rule::enum(Category::class)],
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('presupuestos', 'public');
        }

        // El presupuesto siempre pertenece al usuario autenticado
        $request->user()->shipments()->create($validated + ['status' => ShipmentStatus::Pending]);

        $ruta = $request->user()->isAdmin() ? 'shipments.index' : 'dashboard';

        return redirect()->route($ruta)->with('success', 'Presupuesto enviado correctamente');
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', 'Presupuesto eliminado correctamente');
    }
}
