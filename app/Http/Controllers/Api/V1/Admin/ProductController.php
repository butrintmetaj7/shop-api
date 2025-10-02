<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends ApiController
{
    /**
     * Display a listing of products (admin access).
     */
    public function index()
    {
        $products = Product::paginate();
        
        return $this->successWithPagination($products, ProductResource::class);
    }

    /**
     * Display the specified product (admin access).
     */
    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product));
    }

    /**
     * Update the specified product (admin access).
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }
}

