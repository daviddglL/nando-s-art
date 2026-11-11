<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Todas las rutas de este fichero tienen el límite 'api' (AppServiceProvider)

// Solo lectura: la creación de obras va por la ruta web protegida con 'admin'
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
