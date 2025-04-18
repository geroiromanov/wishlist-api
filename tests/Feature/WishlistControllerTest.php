<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_wishlist()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();
        $user->wishlist()->create(['product_id' => $product->id]);

        $response = $this->getJson('/api/wishlist');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'product_id', 'name', 'price']
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_access_wishlist()
    {
        $response = $this->getJson('/api/wishlist');

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'Error. Unauthenticated.'
            ]);
    }

    public function test_authenticated_user_can_add_product_to_wishlist()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $response = $this->postJson('/api/wishlist', ['product_id' => $product->id]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'product_id', 'name', 'price']
            ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_authenticated_user_cannot_add_same_product_twice()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $this->postJson('/api/wishlist', ['product_id' => $product->id]);

        $response = $this->postJson('/api/wishlist', ['product_id' => $product->id]);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }

    public function test_unauthenticated_user_cannot_add_to_wishlist()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/wishlist', ['product_id' => $product->id]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_validation_fails_without_product_id()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/wishlist', []);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_user_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();
        $user->wishlist()->create(['product_id' => $product->id]);

        $response = $this->deleteJson("/api/wishlist/{$product->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true
            ]);
    }

    public function test_removing_non_existent_product_from_wishlist_returns_false()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/wishlist/{$product->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'success' => false
            ]);
    }

    public function test_unauthenticated_user_cannot_remove_from_wishlist()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/wishlist/{$product->id}");

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'Error. Unauthenticated.'
            ]);
    }
}
