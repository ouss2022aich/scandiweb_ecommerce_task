<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemType
{
    public function create(ObjectType $productType): ObjectType
    {
        return new ObjectType([
            'name' => 'OrderItem',
            'fields' => static fn (): array => [
                'id' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributeItemIds' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                'product' => Type::nonNull($productType),
            ],
        ]);
    }
}
