<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceType
{
    public function __construct(
        private readonly CurrencyType $currencyType,
    ) {
    }

    public function create(): ObjectType
    {
        return new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => Type::nonNull(Type::float()),
                'currency' => Type::nonNull($this->currencyType->create()),
            ],
        ]);
    }
}
