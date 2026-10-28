<?php

use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Usuarios: requiere sesión iniciada; los permisos finos están en UserPolicy
Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('users', UserController::class);
});

// Solo lectura: la creación de obras va por la ruta web protegida con 'admin'
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Subida suelta de imágenes: solo el admin
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::post('/upload-image', [ImageUploadController::class, 'upload']);
});
