<?php

declare(strict_types=1);

namespace App\config\database\seeders;

use App\Models\AllCategory;
use App\Models\Category;
use App\Models\ClothesCategory;
use App\Models\TechCategory;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class CategorySeeder
{
    public function seed(EntityManager $entityManager, array $categories): void
    {
        foreach ($categories as $categoryName) {
            if ($entityManager->find(Category::class, $categoryName) !== null) {
                continue;
            }

            $category = match ($categoryName) {
                'all' => new AllCategory(),
                'clothes' => new ClothesCategory(),
                'tech' => new TechCategory(),
                default => throw new InvalidArgumentException(sprintf('Unsupported category "%s".', $categoryName)),
            };

            $entityManager->persist($category);
        }
    }
}
