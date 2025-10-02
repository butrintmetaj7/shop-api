<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductQueryService
{
    public function getFilteredProducts(array $filters = []): LengthAwarePaginator
    {
        $perPage = $this->getPerPage($filters['per_page'] ?? null);

        return Product::query()
            ->search($filters['search'] ?? null)
            ->filterByCategory($filters['category'] ?? null)
            ->filterByPriceRange(
                $filters['min_price'] ?? null,
                $filters['max_price'] ?? null
            )
            ->paginate($perPage);
    }

    private function getPerPage(?int $perPage): int
    {
        if ($perPage === null) {
            return config('app.pagination.default_per_page');
        }

        return min($perPage, config('app.pagination.max_per_page'));
    }
}

