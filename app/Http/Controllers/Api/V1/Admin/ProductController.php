<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\AdminProductResource;
use App\Http\Requests\Product\UpdateProductRequest;
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
        
        return $this->successWithPagination($products, AdminProductResource::class);
    }

    public function show(Product $product)
    {
        return $this->successResponse(new AdminProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse(new AdminProductResource($product), 'Product updated successfully');
    }
}

