<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::get('products', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist', [WishlistController::class, 'store']);
    Route::delete('wishlist/{product}', [WishlistController::class, 'destroy']);

});
