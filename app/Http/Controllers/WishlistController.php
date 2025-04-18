<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddWishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Services\WishlistService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WishlistController extends Controller
{
    public function __construct(
        protected WishlistService $wishlistService,
    )
    {
    }

    #[OA\Get(
        path: '/api/wishlist',
        summary: 'Get the current user`s wishlist',
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
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $wishlists = $user->wishlist()->with('product')->get();

        $wishlistCollection = WishlistResource::collection($wishlists);

        return response()->json([
            'success' => true,
            'data' => $wishlistCollection,
        ], Response::HTTP_OK);
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
            new OA\Response(response: Response::HTTP_CREATED, description: 'Product added to wishlist.'),
            new OA\Response(response: Response::HTTP_CONFLICT, description: 'Product already in wishlist.'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error.'),
        ]
    )]
    public function store(AddWishlistRequest $request): JsonResponse
    {
        $user = $request->user();
        $productId = $request->input('product_id');
        $wishlist = $this->wishlistService->add($user, $productId);

        $wishlistResource = new WishlistResource($wishlist);

        return response()->json([
            'success' => true,
            'data' => $wishlistResource
        ], Response::HTTP_CREATED);
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
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Product not found in wishlist'),
        ]
    )]
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $productId = $product->id;

        $isRemoved = $this->wishlistService->remove($user, $productId);

        return response()->json([
            'success' => $isRemoved,
        ], Response::HTTP_OK);
    }
}
