<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

//Route::middleware('auth:sanctum')->post('/tokens/create', [AuthController::class, 'createToken']);

Route::get('products', [ProductController::class, 'index']);
Route::middleware('auth:sanctum')->post('products', [ProductController::class, 'store']);
