<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wishlist;
use Symfony\Component\HttpFoundation\Response;

class WishlistService
{
    /**
     * @throws \Exception
     */
    public function add(User $user, int $productId)
    {
        if ($user->hasInWishlist($productId)) {
            throw new \Exception('Product already in wishlist.', Response::HTTP_CONFLICT);
        }

        return $user->wishlist()->create([
            'product_id' => $productId,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function remove(User $user, int $productId): bool
    {
        $wishlist = $user->wishlist()->where('product_id', $productId)->first();

        if (!$wishlist instanceof Wishlist) {
            throw new \Exception('Product not found in wishlist.', Response::HTTP_NOT_FOUND);
        }

        return $wishlist->delete();
    }
}
