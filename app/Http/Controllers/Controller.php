<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API documentation for Wishlist API.',
    title: 'Wishlist API'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
//TODO: add psr fixer, pre-commit hook
abstract class Controller
{
}
