<?php

namespace App\Http\Controllers\Api\V1\Shop;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Services\Product\ProductQueryService;

class ProductController extends ApiController
{
    public function __construct(
        private ProductQueryService $queryService
    ) {}

    public function index()
    {
        $products = $this->queryService->getFilteredProducts(request()->only([
            'search',
            'category',
            'min_price',
            'max_price',
            'per_page'
        ]));
        
        return $this->successWithPagination($products, ProductResource::class);
    }

    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product));
    }
}

