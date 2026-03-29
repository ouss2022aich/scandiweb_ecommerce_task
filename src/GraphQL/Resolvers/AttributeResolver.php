<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

class AttributeResolver
{
    public function resolveAttributes(array $product): array
    {
        return $product['attributes'] ?? [];
    }
}
