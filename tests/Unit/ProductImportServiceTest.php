<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\Product\ProductImportService;
use App\Services\Product\Importers\FakeStoreProductImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $importer = new FakeStoreProductImporter(Http::getFacadeRoot());
        $this->service = new ProductImportService($importer);
    }

    public function test_imports_products_successfully()
    {
        $mockProducts = [
            [
                'id' => 1,
                'title' => 'Test Product',
                'price' => 19.99,
                'description' => 'Test description',
                'category' => 'test',
                'image' => 'https://example.com/image.jpg',
                'rating' => ['rate' => 4.5, 'count' => 100]
            ]
        ];

        Http::fake([
            'https://fakestoreapi.com/products' => Http::response($mockProducts, 200)
        ]);

        $result = $this->service->importProducts();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['processed']);
        $this->assertStringContainsString('Successfully imported 1 products', $result['message']);
        
        $this->assertDatabaseHas('products', [
            'external_source' => 'fakestore',
            'external_id' => 1,
            'title' => 'Test Product',
            'category' => 'test',
            'price' => 19.99
        ]);
    }

    public function test_handles_api_failure()
    {
        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([], 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch products from fakestore: 500');
        
        $this->service->importProducts();
    }

    public function test_handles_empty_response()
    {
        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([], 200)
        ]);

        $result = $this->service->importProducts();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('No products found', $result['message']);
    }

    public function test_updates_existing_products()
    {
        Product::create([
            'title' => 'Test Product',
            'category' => 'test',
            'price' => 10.00,
            'description' => 'Old description',
            'image' => 'old-image.jpg',
            'rating' => ['rate' => 3.0, 'count' => 50],
            'external_source' => 'fakestore',
            'external_id' => 1
        ]);

        $mockProducts = [
            [
                'id' => 1,
                'title' => 'Test Product',
                'price' => 19.99,
                'description' => 'Updated description',
                'category' => 'test',
                'image' => 'new-image.jpg',
                'rating' => ['rate' => 4.5, 'count' => 100]
            ]
        ];

        Http::fake([
            'https://fakestoreapi.com/products' => Http::response($mockProducts, 200)
        ]);

        $result = $this->service->importProducts();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, Product::count());
        
        $product = Product::first();
        $this->assertEquals(19.99, $product->price);
        $this->assertEquals('Updated description', $product->description);
    }

    public function test_can_use_custom_api_url()
    {
        $customUrl = 'https://custom-api.com/products';
        $importer = new FakeStoreProductImporter(Http::getFacadeRoot(), $customUrl);
        $service = new ProductImportService($importer);

        Http::fake([
            $customUrl => Http::response([
                [
                    'id' => 1,
                    'title' => 'Custom Product',
                    'price' => 29.99,
                    'description' => 'Custom description',
                    'category' => 'custom',
                    'image' => 'custom-image.jpg',
                    'rating' => ['rate' => 5.0, 'count' => 200]
                ]
            ], 200)
        ]);

        $result = $service->importProducts();

        $this->assertTrue($result['success']);
        Http::assertSent(function ($request) use ($customUrl) {
            return $request->url() === $customUrl;
        });
    }
}
