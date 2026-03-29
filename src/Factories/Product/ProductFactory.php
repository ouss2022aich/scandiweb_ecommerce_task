<?php

declare(strict_types=1);

namespace App\Factories\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use InvalidArgumentException;

class ProductFactory
{
    /**
     * @param ProductCreatorInterface[] $creators
     */
    public function __construct(
        private readonly array $creators = [
            new TechProductCreator(),
            new ClothesProductCreator(),
        ],
    ) {
    }

    public function create(string $type, string $slug, string $name, Category $category, Brand $brand): Product
    {
        foreach ($this->creators as $creator) {
            if ($creator->supports($type)) {
                return $creator->create($slug, $name, $category, $brand);
            }
        }

        throw new InvalidArgumentException(sprintf('Unsupported product type "%s".', $type));
    }
}
