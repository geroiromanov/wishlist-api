<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', fn () => view('welcome'));
Route::get('/login', fn() => response()->json(null, Response::HTTP_UNAUTHORIZED))->name('login');
