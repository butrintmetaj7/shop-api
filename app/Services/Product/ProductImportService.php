<?php

namespace App\Services\Product;

use App\Contracts\ProductImporterInterface;
use App\Models\Product;

class ProductImportService
{
    public function __construct(
        private ProductImporterInterface $importer
    ) {}

    public function importProducts(): array
    {
        $products = $this->importer->fetchProducts();
        
        if ($products->isEmpty()) {
            return [
                'success' => false,
                'processed' => 0,
                'message' => "No products found from {$this->importer->getSource()}"
            ];
        }

        $processedCount = $this->syncProducts($products);

        return [
            'success' => true,
            'processed' => $processedCount,
            'message' => "Successfully imported {$processedCount} products from {$this->importer->getSource()}"
        ];
    }

    protected function syncProducts($products): int
    {
        $upsertData = $products->map(function ($productData) {
            return $this->importer->transformProduct($productData);
        })->toArray();

        Product::upsert(
            $upsertData,
            ['external_source', 'external_id'],
            ['title', 'price', 'description', 'category', 'image', 'rating', 'updated_at']
        );

        return count($upsertData);
    }

    public function getImporter(): ProductImporterInterface
    {
        return $this->importer;
    }
}
