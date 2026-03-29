<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Doctrine\ORM\EntityManager;

class CategoryService
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {
    }

    public function listCategories(): array
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return array_map(
            fn (Category $category): array => $this->serializeCategory($category),
            $categories,
        );
    }

    public function serializeCategory(Category $category): array
    {
        return [
            'name' => $category->getName(),
        ];
    }
}
