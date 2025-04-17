<?php

namespace App\Http\Controllers;

use App\Models\Product;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        security: [['sanctum' => []]],
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of products',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'T-shirt'),
                            new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                            new OA\Property(property: 'description', type: 'string', example: 'A comfortable cotton T-shirt'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    )
                )
            )
        ]
    )]
    public function index()
    {
        return Product::all();
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'T-shirt'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                    new OA\Property(property: 'description', type: 'string', example: 'A comfortable cotton T-shirt'),
                ]
            )
        ),
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'T-shirt'),
                        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                        new OA\Property(property: 'description', type: 'string', example: 'A comfortable cotton T-shirt'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            )
        ]
    )]
    public function store( $request)
    {
        dd($request);
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

}
