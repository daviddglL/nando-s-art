<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
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

    // Gestión de presupuestos (shipments)
    Route::get('/admin/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::delete('/admin/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');
});

// Ruta para obtener frase del día (proxy para evitar CORS).
// Se cachea hasta medianoche y no deja la página colgada si el servicio externo falla.
Route::get('/frase-proxy', function () {
    $frase = Cache::get('frase-del-dia');

    if (! $frase) {
        try {
            $response = Http::timeout(3)->get('https://frasedeldia.azurewebsites.net/api/phrase');
            if ($response->successful() && $response->json('phrase')) {
                $frase = $response->json();
                Cache::put('frase-del-dia', $frase, now()->endOfDay());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            report($e);
        }
    }

    return $frase
        ? response()->json($frase)
        : response()->json(['message' => 'No se pudo cargar la frase del día'], 503);
})->middleware('throttle:60,1');

// Autenticación

Auth::routes();

// Ruta home (post-login)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth', 'admin']);

Route::middleware(['auth'])->group(function () {
    Route::get('/presupuesto', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/presupuesto', [ShipmentController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('shipments.store');

});


