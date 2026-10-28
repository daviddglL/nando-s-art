<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Todas las rutas de este fichero tienen el límite 'api' (AppServiceProvider)

// Usuarios: requiere sesión iniciada; los permisos finos están en UserPolicy
Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('users', UserController::class);
});

// Solo lectura: la creación de obras va por la ruta web protegida con 'admin'
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
