<?php 
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

// Usuarios: requiere sesión iniciada; los permisos finos están en UserPolicy
Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('users', UserController::class);
});
// Solo lectura: la creación de obras va por la ruta web protegida con 'admin'
Route::apiResource('products', ProductController::class)->only(['index', 'show']);


Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});



Route::post('/upload-image', [ImageUploadController::class, 'upload']);
