<?php

namespace Tests\Feature;

use App\Services\Product\ProductImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportProductsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_runs_successfully()
    {
        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([
                [
                    'id' => 1,
                    'title' => 'Test Product',
                    'price' => 19.99,
                    'description' => 'Test description',
                    'category' => 'test',
                    'image' => 'https://example.com/image.jpg',
                    'rating' => ['rate' => 4.5, 'count' => 100]
                ]
            ], 200)
        ]);

        $this->artisan('products:import')
            ->expectsOutput('Starting product import from Fake Store API...')
            ->expectsOutput('Successfully processed 1 products')
            ->assertExitCode(0);

        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'category' => 'test'
        ]);
    }

    public function test_command_handles_api_failure()
    {
        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([], 500)
        ]);

        $this->artisan('products:import')
            ->expectsOutput('Starting product import from Fake Store API...')
            ->expectsOutput('Error during import: Failed to fetch products from API: 500')
            ->assertExitCode(1);
    }

    public function test_command_handles_service_exception()
    {
        $this->mock(ProductImportService::class, function ($mock) {
            $mock->shouldReceive('importProducts')
                ->once()
                ->andThrow(new \Exception('Service error'));
        });

        $this->artisan('products:import')
            ->expectsOutput('Starting product import from Fake Store API...')
            ->expectsOutput('Error during import: Service error')
            ->assertExitCode(1);
    }
}
