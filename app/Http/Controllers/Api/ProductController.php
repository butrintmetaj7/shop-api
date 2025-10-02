<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\UpdateProductRequest;

class ProductController extends ApiController
{
    public function index()
    {
        $products = Product::paginate();

        return $this->successResponseWithPagination($products, ProductResource::collection($products->items()));
    }

    public function show(Product $product)
    {
        return $this->successResponse('Product fetched successfully', new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse('Product updated successfully', new ProductResource($product));

    }
}
