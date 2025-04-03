<?php 
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('shipments', ShipmentController::class);


use Illuminate\Http\Request;



Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando']);
});

