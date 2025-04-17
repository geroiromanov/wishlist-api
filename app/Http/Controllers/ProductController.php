<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        security: [['sanctum' => []]],
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
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
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json($products, Response::HTTP_OK);
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
                response: Response::HTTP_CREATED,
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
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Validation error'
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json($product, Response::HTTP_CREATED);
    }
}
