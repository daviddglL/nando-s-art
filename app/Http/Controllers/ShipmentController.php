<?php
namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index()
    {
        return Shipment::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'status' => 'required',
        ]);

        return Shipment::create($request->all());
    }

    public function show(Shipment $shipment)
    {
        return $shipment;
    }

    public function update(Request $request, Shipment $shipment)
    {
        $shipment->update($request->all());
        return $shipment;
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return response()->json(['message' => 'Shipment deleted']);
    }
}
