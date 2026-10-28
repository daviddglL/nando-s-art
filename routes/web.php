<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncargoController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Ruta de inicio
Route::view('/', 'landing')->name('landing');

// Ruta pública para ver la galería (solo visualización)
Route::get('/galeria', [GaleriaController::class, 'index'])->name('galeria');

// Ficha pública de cada obra, con URL para compartir
Route::get('/obras/{obra:slug}', [ObraController::class, 'show'])->name('obras.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

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
        } catch (HttpClientException $e) {
            report($e);
        }
    }

    return $frase
        ? response()->json($frase)
        : response()->json(['message' => 'No se pudo cargar la frase del día'], 503);
})->middleware('throttle:60,1');

// Autenticación (laravel/ui): login, registro, recuperar y confirmar contraseña
Auth::routes();

// Usuarios con sesión iniciada
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/presupuesto', [EncargoController::class, 'create'])->name('encargos.create');
    Route::post('/presupuesto', [EncargoController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('encargos.store');
    // Cada cliente ve el detalle de los suyos (EncargoPolicy)
    Route::get('/presupuestos/{encargo}', [EncargoController::class, 'show'])->name('encargos.show');
});

// Rutas solo para admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');

    Route::get('/galeria/nuevo', [ProductController::class, 'create'])->name('products.create');
    Route::post('/galeria', [ProductController::class, 'store'])->name('products.store');

    // Gestión de obras: listar, editar, ordenar, borrar (a la papelera) y restaurar
    Route::get('/admin/obras', [ProductController::class, 'listado'])->name('admin.obras.index');
    Route::get('/admin/obras/{obra}/editar', [ProductController::class, 'edit'])->name('admin.obras.edit');
    Route::put('/admin/obras/{obra}', [ProductController::class, 'update'])->name('admin.obras.update');
    Route::delete('/admin/obras/{obra}', [ProductController::class, 'destroy'])->name('admin.obras.destroy');
    Route::patch('/admin/obras/{obra}/restaurar', [ProductController::class, 'restore'])
        ->withTrashed()
        ->name('admin.obras.restore');
    Route::patch('/admin/obras/{obra}/mover', [ProductController::class, 'mover'])->name('admin.obras.mover');

    // Gestión de encargos (presupuestos recibidos)
    Route::get('/admin/encargos', [EncargoController::class, 'index'])->name('encargos.index');
    Route::patch('/admin/encargos/{encargo}', [EncargoController::class, 'update'])->name('encargos.update');
    Route::delete('/admin/encargos/{encargo}', [EncargoController::class, 'destroy'])->name('encargos.destroy');
    Route::permanentRedirect('/admin/shipments', '/admin/encargos');
});
