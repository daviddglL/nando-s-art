<?php

use App\Http\Controllers\ObraController;
use Illuminate\Support\Facades\Route;

// Todas las rutas de este fichero tienen el límite 'api' (AppServiceProvider)

// Solo lectura: la galería pide aquí las obras filtradas
Route::get('products', [ObraController::class, 'index'])->name('api.obras.index');
Route::get('products/{slug}', [ObraController::class, 'api'])->name('api.obras.show');
