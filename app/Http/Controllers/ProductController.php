<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
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

        $productCollection = ProductResource::collection($products);

        return response()->json([
            'success' => true,
            'data' => $productCollection
        ], Response::HTTP_OK);
    }
}
