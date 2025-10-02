<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ProductImporterInterface
{
    public function fetchProducts(): Collection;

    public function transformProduct(array $rawProductData): array;

    public function getSource(): string;
}

