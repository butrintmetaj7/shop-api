<?php

namespace App\Http\Controllers\Api\V1\Shop;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends ApiController
{
    /**
     * Display a listing of products (public access).
     */
    public function index()
    {
        $products = Product::paginate();
        
        return $this->successWithPagination($products, ProductResource::class);
    }

    /**
     * Display the specified product (public access).
     */
    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product));
    }
}

