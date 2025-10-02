<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/shop/products');

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

    public function test_products_list_returns_correct_resource_structure()
    {
        Product::factory()->create();

        $response = $this->getJson('/api/v1/shop/products');

        $response->assertStatus(200);
        
        $products = $response->json('data');
        $firstProduct = $products[0];

        $expectedKeys = ['id', 'title', 'price', 'description', 'category', 'image'];
        $actualKeys = array_keys($firstProduct);
        
        $this->assertEquals($expectedKeys, $actualKeys, 'Product resource should have exactly these keys: ' . implode(', ', $expectedKeys));
    }

    public function test_products_list_is_paginated()
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/shop/products');

        $response->assertStatus(200);

        $data = $response->json();

        $this->assertArrayHasKey('current_page', $data);
        $this->assertArrayHasKey('per_page', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('last_page', $data);
        $this->assertArrayHasKey('from', $data);
        $this->assertArrayHasKey('to', $data);
        $this->assertArrayHasKey('data', $data);
        
        $this->assertIsArray($data['data']);
    }

    public function test_can_show_single_product()
    {
        $product = Product::factory()->create();
        
        $response = $this->getJson("/api/v1/shop/products/{$product->id}");

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
        $response = $this->getJson('/api/v1/shop/products/9999');

        $response->assertStatus(404);
    }

    public function test_public_access_does_not_require_authentication()
    {
        // This test verifies that shop routes are truly public
        Product::factory()->create();
        
        // Make request without authentication
        $response = $this->getJson('/api/v1/shop/products');
        
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}

