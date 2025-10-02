<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

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
