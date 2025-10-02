<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('title', 'like', "%{$search}%");
    }

    public function scopeFilterByCategory(Builder $query, ?string $category): Builder
    {
        if (empty($category)) {
            return $query;
        }

        return $query->where('category', $category);
    }

    public function scopeFilterByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice): Builder
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }
}

