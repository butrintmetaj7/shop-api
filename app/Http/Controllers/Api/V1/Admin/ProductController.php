<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends ApiController
{
    public function index()
    {
        $products = Product::paginate();
        
        return $this->successWithPagination($products, ProductResource::class);
    }

    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse(new ProductResource($product), 'Product updated successfully');
    }
}

