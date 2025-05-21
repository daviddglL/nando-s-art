<?php 
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('shipments', ShipmentController::class);


use Illuminate\Http\Request;
use App\Models\Product;

Route::get('/products', function (\Illuminate\Http\Request $request) {
    $style = $request->query('style');
    return Product::where('style', $style)->get();
});


Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});



Route::post('/upload-image', [ImageUploadController::class, 'upload']);
