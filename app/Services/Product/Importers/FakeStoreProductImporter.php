<?php

namespace App\Services\Product\Importers;

use App\Contracts\ProductImporterInterface;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Collection;

class FakeStoreProductImporter implements ProductImporterInterface
{
    public function __construct(
        private HttpClient $httpClient,
        private ?string $apiUrl = null
    ) {
        $this->apiUrl = $apiUrl ?? config('services.fake_store.api_url');
    }

    public function fetchProducts(): Collection
    {
        $response = $this->httpClient->get($this->apiUrl);

        if (!$response->successful()) {
            throw new \Exception(
                "Failed to fetch products from {$this->getSource()}: {$response->status()}"
            );
        }

        return collect($response->json());
    }

    public function transformProduct(array $rawProductData): array
    {
        return [
            'external_source' => $this->getSource(),
            'external_id' => $rawProductData['id'],
            'title' => $rawProductData['title'],
            'price' => $rawProductData['price'],
            'description' => $rawProductData['description'],
            'category' => $rawProductData['category'],
            'image' => $rawProductData['image'],
            'rating' => json_encode($rawProductData['rating']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function getSource(): string
    {
        return 'fakestore';
    }
}

