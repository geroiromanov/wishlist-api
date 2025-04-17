<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::middleware('auth:sanctum')->post('products', [ProductController::class, 'store']);
});
