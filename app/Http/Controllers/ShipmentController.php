<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ShipmentController extends Controller
{
    public function create()
    {
        return view('shipments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required',
            'height' => 'required|numeric',
            'width' => 'required|numeric',
            'style' => 'required|string',
            'category' => 'required|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $shipment = new Shipment();
        $shipment->user_id = Auth::id();
        $shipment->description = $validated['description'];
        $shipment->height = $validated['height'];
        $shipment->width = $validated['width'];
        $shipment->style = $validated['style'];
        $shipment->category = $validated['category'];
        $shipment->status = 'pending';

        if ($request->hasFile('imagen')) {
            $shipment->imagen = $request->file('imagen')->store('shipments', 'public');
        }

        $shipment->save();

        return redirect()->route('dashboard')->with('success', 'Pedido enviado correctamente');
    }
}
