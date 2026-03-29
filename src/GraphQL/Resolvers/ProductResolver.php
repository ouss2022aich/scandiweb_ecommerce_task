<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use App\Services\ProductService;

class ProductResolver
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function resolveProducts(array $args): array
    {
        return $this->productService->listProducts($args['category'] ?? null);
    }

    public function resolveProduct(array $args): ?array
    {
        return $this->productService->getProduct($args['slug']);
    }

    public function resolveCreateProduct(array $args, array $context): array
    {
        return $this->productService->createProduct(
            $args['input'],
            $context['uploadedFiles'] ?? [],
        );
    }
}
