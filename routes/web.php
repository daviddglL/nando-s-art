<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncargoController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\ObraImagenController;
use App\Http\Controllers\PaginaController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Portada, con las obras destacadas
Route::get('/', PortadaController::class)->name('landing');

// Ruta pública para ver la galería (solo visualización)
Route::get('/galeria', [GaleriaController::class, 'index'])->name('galeria');

// Ficha pública de cada obra, con URL para compartir
Route::get('/obras/{obra:slug}', [ObraController::class, 'show'])->name('obras.show');

// Páginas de texto que el artista edita desde el panel
Route::get('/sobre-mi', [PaginaController::class, 'sobreMi'])->name('paginas.sobre-mi');
Route::get('/contacto', [PaginaController::class, 'contacto'])->name('paginas.contacto');

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

// Autenticación (laravel/ui): login, registro (con verificación de email), recuperar y confirmar contraseña
Auth::routes(['verify' => true]);

// Usuarios con sesión iniciada (el perfil no pide el email verificado, para poder corregirlo)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Presupuestos: solo con el email verificado, para que nadie mande peticiones en bucle con cuentas falsas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/presupuesto', [EncargoController::class, 'create'])->name('encargos.create');
    Route::post('/presupuesto', [EncargoController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('encargos.store');
    // Cada cliente ve el detalle de los suyos (EncargoPolicy)
    Route::get('/presupuestos/{encargo}', [EncargoController::class, 'show'])->name('encargos.show');
    Route::get('/presupuestos/{encargo}/imagen', [EncargoController::class, 'imagen'])->name('encargos.imagen');
});

// Rutas solo para admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');

    Route::get('/galeria/nuevo', [ProductController::class, 'create'])->name('products.create');
    Route::post('/galeria', [ProductController::class, 'store'])->name('products.store');

    // Gestión de obras: listar, editar, ordenar, borrar (a la papelera), restaurar y eliminar definitivamente
    Route::get('/admin/obras', [ProductController::class, 'listado'])->name('admin.obras.index');
    Route::get('/admin/obras/{obra}/editar', [ProductController::class, 'edit'])->name('admin.obras.edit');
    Route::put('/admin/obras/{obra}', [ProductController::class, 'update'])->name('admin.obras.update');
    Route::delete('/admin/obras/{obra}', [ProductController::class, 'destroy'])->name('admin.obras.destroy');
    Route::patch('/admin/obras/{obra}/restaurar', [ProductController::class, 'restore'])
        ->withTrashed()
        ->name('admin.obras.restore');
    Route::delete('/admin/obras/{obra}/definitivo', [ProductController::class, 'forceDestroy'])
        ->withTrashed()
        ->name('admin.obras.force-destroy');
    Route::patch('/admin/obras/{obra}/mover', [ProductController::class, 'mover'])->name('admin.obras.mover');
    Route::patch('/admin/obras/{obra}/destacar', [ProductController::class, 'destacar'])->name('admin.obras.destacar');

    // Imágenes adicionales de cada obra: subir varias, ordenarlas, elegir la principal y borrarlas
    Route::post('/admin/obras/{obra}/imagenes', [ObraImagenController::class, 'store'])->name('admin.obras.imagenes.store');
    Route::patch('/admin/obras/{obra}/imagenes/{imagen}/mover', [ObraImagenController::class, 'mover'])->name('admin.obras.imagenes.mover');
    Route::patch('/admin/obras/{obra}/imagenes/{imagen}/principal', [ObraImagenController::class, 'principal'])->name('admin.obras.imagenes.principal');
    Route::delete('/admin/obras/{obra}/imagenes/{imagen}', [ObraImagenController::class, 'destroy'])->name('admin.obras.imagenes.destroy');

    // Gestión de encargos (presupuestos recibidos)
    Route::get('/admin/encargos', [EncargoController::class, 'index'])->name('encargos.index');
    Route::patch('/admin/encargos/{encargo}', [EncargoController::class, 'update'])->name('encargos.update');
    Route::delete('/admin/encargos/{encargo}', [EncargoController::class, 'destroy'])->name('encargos.destroy');
    Route::permanentRedirect('/admin/shipments', '/admin/encargos');

    // Textos de "Sobre mí" y "Contacto"
    Route::get('/admin/paginas', [PaginaController::class, 'index'])->name('admin.paginas.index');
    Route::get('/admin/paginas/{pagina}/editar', [PaginaController::class, 'edit'])->name('admin.paginas.edit');
    Route::put('/admin/paginas/{pagina}', [PaginaController::class, 'update'])->name('admin.paginas.update');
});
