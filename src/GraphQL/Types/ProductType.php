<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\GraphQL\Resolvers\AttributeResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType
{
    public function __construct(
        private readonly PriceType $priceType,
        private readonly AttributeType $attributeType,
        private readonly AttributeResolver $attributeResolver,
    ) {
    }

    public function create(): ObjectType
    {
        return new ObjectType([
            'name' => 'Product',
            'fields' => fn (): array => [
                'id' => Type::nonNull(Type::string()),
                'slug' => Type::nonNull(Type::string()),
                'type' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'description' => Type::string(),
                'inStock' => Type::nonNull(Type::boolean()),
                'category' => Type::nonNull(Type::string()),
                'brand' => Type::nonNull(Type::string()),
                'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                'prices' => Type::nonNull(Type::listOf(Type::nonNull($this->priceType->create()))),
                'attributes' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull($this->attributeType->create()))),
                    'resolve' => fn (array $product): array => $this->attributeResolver->resolveAttributes($product),
                ],
            ],
        ]);
    }
}
