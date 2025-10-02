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
            ->expectsOutput('Starting product import from fakestore...')
            ->expectsOutput('Successfully imported 1 products from fakestore')
            ->assertExitCode(0);

        $this->assertDatabaseHas('products', [
            'external_source' => 'fakestore',
            'external_id' => 1,
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
            ->expectsOutput('Starting product import from fakestore...')
            ->expectsOutput('Error during import: Failed to fetch products from fakestore: 500')
            ->assertExitCode(1);
    }

    public function test_command_supports_different_sources()
    {
        Http::fake([
            'https://fakestoreapi.com/products' => Http::response([
                [
                    'id' => 1,
                    'title' => 'Fake Store Product',
                    'price' => 19.99,
                    'description' => 'Test description',
                    'category' => 'test',
                    'image' => 'https://example.com/image.jpg',
                    'rating' => ['rate' => 4.5, 'count' => 100]
                ]
            ], 200)
        ]);

        $this->artisan('products:import', ['--source' => 'fakestore'])
            ->expectsOutput('Starting product import from fakestore...')
            ->assertExitCode(0);
    }

    public function test_command_fails_with_invalid_source()
    {
        $this->artisan('products:import', ['--source' => 'invalid'])
            ->expectsOutput('Error during import: Unknown import source: invalid')
            ->assertExitCode(1);
    }
}
