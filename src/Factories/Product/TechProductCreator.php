<?php

declare(strict_types=1);

namespace App\Factories\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\TechProduct;

class TechProductCreator implements ProductCreatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'tech';
    }

    public function create(string $slug, string $name, Category $category, Brand $brand): Product
    {
        return new TechProduct($slug, $name, $category, $brand);
    }
}
