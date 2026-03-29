<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType
{
    public function __construct(
        private readonly OrderItemType $orderItemType,
    ) {
    }

    public function create(ObjectType $productType): ObjectType
    {
        $orderItemType = $this->orderItemType->create($productType);

        return new ObjectType([
            'name' => 'Order',
            'fields' => static fn (): array => [
                'id' => Type::nonNull(Type::string()),
                'createdAt' => Type::nonNull(Type::string()),
                'items' => Type::nonNull(Type::listOf(Type::nonNull($orderItemType))),
            ],
        ]);
    }
}
