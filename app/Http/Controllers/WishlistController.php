<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;


class WishlistController extends Controller
{
    #[OA\Get(
        path: '/api/wishlist',
        summary: 'Get the authenticated user\'s wishlist',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Wishlist retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'wishlist',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'product_id', type: 'integer', example: 2),
                                    new OA\Property(property: 'name', type: 'string', example: 'T-shirt'),
                                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                    new OA\Property(property: 'description', type: 'string', example: 'High-quality cotton T-shirt'),
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlist()->with('product')->get();

        return response()->json([
            'wishlist' => $wishlist->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'description' => $item->product->description,
                ];
            }),
        ]);
    }

    #[OA\Post(
        path: '/api/wishlist',
        summary: 'Add a product to the user\'s wishlist',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                ]
            )
        ),
        tags: ['Wishlist'],
        responses: [
            new OA\Response(response: Response::HTTP_CREATED, description: 'Product added to wishlist'),
            new OA\Response(response: Response::HTTP_CONFLICT, description: 'Product already in wishlist'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error')
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();

        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Product already in wishlist'], Response::HTTP_CONFLICT);
        }

        $user->wishlist()->create([
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Product added to wishlist'], Response::HTTP_CREATED);
    }

    #[OA\Delete(
        path: '/api/wishlist/{product}',
        summary: 'Remove a product from the user`s wishlist',
        security: [['sanctum' => []]],
        tags: ['Wishlist'],
        parameters: [
            new OA\Parameter(
                name: 'product',
                description: 'ID of the product to remove from wishlist',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Product removed from wishlist'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Product not found in wishlist')
        ]
    )]
    public function destroy(Request $request, Product $product)
    {
        $deleted = $request->user()->wishlist()
            ->where('product_id', $product->id)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Product removed from wishlist']);
        }

        return response()->json(['message' => 'Product not found in wishlist'], Response::HTTP_NOT_FOUND);
    }


}
