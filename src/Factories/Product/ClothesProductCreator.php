<?php

declare(strict_types=1);

namespace App\Factories\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ClothesProduct;
use App\Models\Product;

class ClothesProductCreator implements ProductCreatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'clothes';
    }

    public function create(string $slug, string $name, Category $category, Brand $brand): Product
    {
        return new ClothesProduct($slug, $name, $category, $brand);
    }
}
