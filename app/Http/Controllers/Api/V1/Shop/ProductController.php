<?php

namespace App\Http\Controllers\Api\V1\Shop;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;

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
}

