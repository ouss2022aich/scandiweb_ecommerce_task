<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Services\CategoryService;

class CategoryResolver
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    public function resolveCategories(): array
    {
        return $this->categoryService->listCategories();
    }
}
