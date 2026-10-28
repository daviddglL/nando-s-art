<?php

namespace App\Http\Controllers;

use App\Enums\EstadoEncargo;
use App\Models\Encargo;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $resumen = [
            'obras' => Product::count(),
            'pendientes' => Encargo::where('status', EstadoEncargo::Pending)->count(),
            'encargos' => Encargo::count(),
        ];

        return view('admin.dashboard', compact('user', 'resumen'));
    }
}
