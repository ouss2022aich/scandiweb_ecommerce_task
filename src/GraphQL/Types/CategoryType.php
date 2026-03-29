<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType
{
    public function create(): ObjectType
    {
        return new ObjectType([
            'name' => 'Category',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
            ],
        ]);
    }
}
