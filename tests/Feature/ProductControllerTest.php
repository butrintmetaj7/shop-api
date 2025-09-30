<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
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
                ]
            ]);

        $this->assertCount(3, $response->json('data.data'));
    }

    public function test_products_list_returns_correct_resource_structure()
    {
        Product::factory()->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        
        $products = $response->json('data.data');
        $firstProduct = $products[0];

        $expectedKeys = ['id', 'title', 'price', 'description', 'category', 'image'];
        $actualKeys = array_keys($firstProduct);
        
        $this->assertEquals($expectedKeys, $actualKeys, 'Product resource should have exactly these keys: ' . implode(', ', $expectedKeys));
    }

    public function test_products_list_is_paginated_with_10_per_page()
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(1, $data['current_page']);
        $this->assertEquals(10, $data['per_page']);
        $this->assertEquals(15, $data['total']);
        $this->assertEquals(2, $data['last_page']);
        $this->assertCount(10, $data['data']);
    }

    public function test_can_show_single_product()
    {
        $product = Product::factory()->create();
        
        $response = $this->getJson("/api/products/{$product->id}");

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

        $productData = $response->json('data');
        $expectedKeys = ['id', 'title', 'price', 'description', 'category', 'image'];
        $actualKeys = array_keys($productData);
       
        $this->assertEquals($expectedKeys, $actualKeys);
    }

    public function test_show_product_returns_404_for_nonexistent_product()
    {
        $response = $this->getJson('/api/products/9999');

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_access_products()
    {
        $this->refreshApplication();
        
        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    }

    public function test_can_update_all_editable_fields()
    {
        $product = Product::factory()->create();
        
        $updateData = [
            'title' => 'Updated Product Title',
            'price' => 149.99,
            'description' => 'Updated product description',
            'image' => 'https://example.com/updated-image.jpg',
        ];
        
        $response = $this->putJson("/api/products/{$product->id}", $updateData);

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
        $product = Product::factory()->create();
        
        $response = $this->putJson("/api/products/{$product->id}", [
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
        $product = Product::factory()->create();
        
        $updateData = [
            'title' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->sentence(10),
            'image' => fake()->imageUrl(640, 480, 'products', true)
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', $updateData + ['id' => $product->id]);
    }

    public function test_update_returns_404_for_nonexistent_product()
    {
        $response = $this->putJson('/api/products/9999', [
            'title' => fake()->words(3, true)
        ]);

        $response->assertStatus(404);
    }

}
