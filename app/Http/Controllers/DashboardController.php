<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $encargos = $request->user()->encargos()->latest()->get();

        return view('dashboard', compact('encargos'));
    }
}
