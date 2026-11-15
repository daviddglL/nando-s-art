<?php

use App\Http\Controllers\FraseController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\MensajeContactoController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\PaginaController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// La web es solo de lectura: las obras y los textos están en resources/contenido/ (sin base de datos ni usuarios)

// Portada, con las obras destacadas
Route::get('/', PortadaController::class)->name('landing');

// Galería con filtros por técnica, tema y texto
Route::get('/galeria', [GaleriaController::class, 'index'])->name('galeria');

// Ficha de cada obra, con URL para compartir
Route::get('/obras/{slug}', [ObraController::class, 'show'])->name('obras.show');

// Páginas de texto
Route::get('/sobre-mi', [PaginaController::class, 'sobreMi'])->name('paginas.sobre-mi');
Route::get('/contacto', [PaginaController::class, 'contacto'])->name('paginas.contacto');

// Formulario de contacto: manda un email al artista
Route::post('/contacto', MensajeContactoController::class)
    ->middleware('throttle:contacto')
    ->name('contacto.enviar');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Frase del día (proxy para evitar CORS), en un controlador para que `route:cache` funcione
Route::get('/frase-proxy', FraseController::class)->middleware('throttle:60,1');
