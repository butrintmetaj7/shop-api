<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Collection;

class ProductImportService
{
    public function __construct(
        private HttpClient $httpClient,
        private ?string $apiUrl = null
    ) {
        $this->apiUrl = $apiUrl ?? config('services.fake_store.api_url');
    }

    public function importProducts(): array
    {
        $products = $this->fetchProductsFromApi();
        
        if ($products->isEmpty()) {
            return ['success' => false, 'message' => 'No products found'];
        }

        $processedCount = $this->syncProducts($products);

        return [
            'success' => true,
            'processed' => $processedCount,
            'message' => "Successfully processed {$processedCount} products"
        ];
    }

    public function fetchProductsFromApi(): Collection
    {
        $response = $this->httpClient->get($this->apiUrl);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch products from API: ' . $response->status());
        }

        return collect($response->json());
    }

    public function syncProducts(Collection $products): int
    {
        $upsertData = $products->map(function ($productData) {
            return $this->transformProductData($productData);
        })->toArray();

        Product::upsert(
            $upsertData,
            ['title', 'category'],
            ['price', 'description', 'image', 'rating', 'updated_at']
        );

        return count($upsertData);
    }

    protected function transformProductData(array $productData): array
    {
        return [
            'title' => $productData['title'],
            'price' => $productData['price'],
            'description' => $productData['description'],
            'category' => $productData['category'],
            'image' => $productData['image'],
            'rating' => json_encode($productData['rating']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
