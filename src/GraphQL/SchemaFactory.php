<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\OrderResolver;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\OrderType;
use App\GraphQL\Types\ProductType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

class SchemaFactory
{
    public function __construct(
        private readonly CategoryResolver $categoryResolver,
        private readonly ProductResolver $productResolver,
        private readonly OrderResolver $orderResolver,
        private readonly CategoryType $categoryType,
        private readonly ProductType $productType,
        private readonly OrderType $orderType,
    ) {
    }

    public function create(): Schema
    {
        $categoryType = $this->categoryType->create();
        $productType = $this->productType->create();
        $orderType = $this->orderType->create($productType);

        $priceInputType = new InputObjectType([
            'name' => 'PriceInput',
            'fields' => [
                'amount' => Type::nonNull(Type::float()),
                'currencyLabel' => Type::nonNull(Type::string()),
            ],
        ]);

        $productInputType = new InputObjectType([
            'name' => 'ProductInput',
            'fields' => [
                'type' => Type::nonNull(Type::string()),
                'slug' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'description' => Type::string(),
                'inStock' => Type::boolean(),
                'category' => Type::nonNull(Type::string()),
                'brand' => Type::nonNull(Type::string()),
                'gallery' => Type::listOf(Type::nonNull(Type::string())),
                'prices' => Type::listOf(Type::nonNull($priceInputType)),
                'attributeItemIds' => Type::listOf(Type::nonNull(Type::int())),
            ],
        ]);

        $orderItemInputType = new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'productSlug' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributeItemIds' => Type::listOf(Type::nonNull(Type::int())),
            ],
        ]);

        $orderInputType = new InputObjectType([
            'name' => 'OrderInput',
            'fields' => [
                'items' => Type::nonNull(Type::listOf(Type::nonNull($orderItemInputType))),
            ],
        ]);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull($categoryType))),
                    'resolve' => fn (): array => $this->categoryResolver->resolveCategories(),
                ],
                'products' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull($productType))),
                    'args' => [
                        'category' => ['type' => Type::string()],
                    ],
                    'resolve' => fn ($rootValue, array $args): array => $this->productResolver->resolveProducts($args),
                ],
                'product' => [
                    'type' => $productType,
                    'args' => [
                        'slug' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => fn ($rootValue, array $args): ?array => $this->productResolver->resolveProduct($args),
                ],
                'orders' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull($orderType))),
                    'resolve' => fn (): array => $this->orderResolver->resolveOrders(),
                ],
                'order' => [
                    'type' => $orderType,
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => fn ($rootValue, array $args): ?array => $this->orderResolver->resolveOrder($args),
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createProduct' => [
                    'type' => Type::nonNull($productType),
                    'args' => [
                        'input' => ['type' => Type::nonNull($productInputType)],
                    ],
                    'resolve' => fn ($rootValue, array $args, array $context): array => $this->productResolver->resolveCreateProduct($args, $context),
                ],
                'createOrder' => [
                    'type' => Type::nonNull($orderType),
                    'args' => [
                        'input' => ['type' => Type::nonNull($orderInputType)],
                    ],
                    'resolve' => fn ($rootValue, array $args): array => $this->orderResolver->resolveCreateOrder($args),
                ],
            ],
        ]);

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType),
        );
    }
}
