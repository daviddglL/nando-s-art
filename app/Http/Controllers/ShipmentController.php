<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'style' => 'required|string',
            'category' => 'required|string',
            // ...otros campos si los hay...
        ]);

        Shipment::create([
            'user_id' => $request->user_id,
            'description' => $request->description,
            'height' => $request->height,
            'width' => $request->width,
            'style' => $request->style,
            'category' => $request->category,
            'status' => 'pending',
            // ...otros campos si los hay...
        ]);

        return redirect()->route('shipments.index')->with('success', 'Shipment creado correctamente');
    }

    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', 'Shipment eliminado correctamente');
    }
}
