<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShipmentController;

// Ruta de inicio
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Ruta protegida del dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Perfil del usuario
Route::view('/profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Ruta pública para ver la galería (solo visualización)
Route::get('/galeria', [GaleriaController::class, 'index'])->name('galeria');

// Rutas solo para admin (añadir productos)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/galeria/nuevo', [ProductController::class, 'create'])->name('products.create');
    Route::post('/galeria', [ProductController::class, 'store'])->name('products.store');
});

// Ruta para obtener frase del día (proxy para evitar CORS)
Route::get('/frase-proxy', function () {
    $response = Http::get('https://frasedeldia.azurewebsites.net/api/phrase');
    return response()->json($response->json());
});

// Autenticación
require __DIR__.'/auth.php';
Auth::routes();

// Ruta home (post-login)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'admin']);

Route::middleware(['auth'])->group(function () {
    Route::get('/presupuesto', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/presupuesto', [ShipmentController::class, 'store'])->name('shipments.store');
});


