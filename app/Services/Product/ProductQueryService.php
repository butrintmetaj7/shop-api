<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductQueryService
{
    private const DEFAULT_PER_PAGE = 15;
    private const MAX_PER_PAGE = 100;

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
            return self::DEFAULT_PER_PAGE;
        }

        return min($perPage, self::MAX_PER_PAGE);
    }
}

