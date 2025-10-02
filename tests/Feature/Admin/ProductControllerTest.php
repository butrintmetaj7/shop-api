<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_products()
    {
        $response = $this->getJson('/api/v1/admin/products');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_list_products()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/admin/products');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ]);
    }

    public function test_non_admin_user_cannot_view_product()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $product = Product::factory()->create();

        $response = $this->getJson("/api/v1/admin/products/{$product->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ]);
    }

    public function test_non_admin_user_cannot_update_product()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $product = Product::factory()->create();

        $response = $this->putJson("/api/v1/admin/products/{$product->id}", [
            'title' => 'Updated Title',
            'price' => 99.99,
            'description' => 'Updated description',
            'image' => 'https://example.com/image.jpg',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ]);

        // Verify product was NOT updated
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_can_list_products()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/admin/products');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                        '*' => [
                            'id',
                            'title',
                            'price',
                            'category',
                            'image'
                        ]
                    ],
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_show_single_product()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        $product = Product::factory()->create();
        
        $response = $this->getJson("/api/v1/admin/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                    'category' => $product->category,
                    'image' => $product->image
                ]
            ]);
    }

    public function test_show_product_returns_404_for_nonexistent_product()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        $response = $this->getJson('/api/v1/admin/products/9999');

        $response->assertStatus(404);
    }

    public function test_can_update_all_editable_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        $product = Product::factory()->create();
        
        $updateData = [
            'title' => 'Updated Product Title',
            'price' => 149.99,
            'description' => 'Updated product description',
            'image' => 'https://example.com/updated-image.jpg',
        ];
        
        $response = $this->putJson("/api/v1/admin/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $updateData
            ]);

        $this->assertDatabaseHas('products', array_merge(
            ['id' => $product->id],
            $updateData
        ));
    }

    public function test_update_validates_input()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        $product = Product::factory()->create();
        
        $response = $this->putJson("/api/v1/admin/products/{$product->id}", [
            'title' => str_repeat('a', 256),
            'price' => -10,
            'description' => '',
            'image' => 'not-a-url',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'price', 'description', 'image']);
    }

    public function test_update_product_with_all_editable_fields()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        $product = Product::factory()->create();
        
        $updateData = [
            'title' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->sentence(10),
            'image' => fake()->imageUrl(640, 480, 'products', true)
        ];

        $response = $this->putJson("/api/v1/admin/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', $updateData + ['id' => $product->id]);
    }

    public function test_update_returns_404_for_nonexistent_product()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        $response = $this->putJson('/api/v1/admin/products/9999', [
            'title' => fake()->words(3, true)
        ]);

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_update_product()
    {
        $product = Product::factory()->create();
        
        $response = $this->putJson("/api/v1/admin/products/{$product->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(401);
    }
}

