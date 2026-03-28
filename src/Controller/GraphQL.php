<?php

namespace App\Controller;

use App\Services\ProductService;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Error\DebugFlag;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle(array $vars = []) {
        try {
            $productService = new ProductService(require __DIR__ . '/../../config/database/doctrine.php');

            $currencyType = new ObjectType([
                'name' => 'Currency',
                'fields' => [
                    'label' => Type::nonNull(Type::string()),
                    'symbol' => Type::nonNull(Type::string()),
                ],
            ]);

            $priceType = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'amount' => Type::nonNull(Type::float()),
                    'currency' => Type::nonNull($currencyType),
                ],
            ]);

            $attributeItemType = new ObjectType([
                'name' => 'AttributeItem',
                'fields' => [
                    'id' => Type::nonNull(Type::string()),
                    'displayValue' => Type::nonNull(Type::string()),
                    'value' => Type::nonNull(Type::string()),
                ],
            ]);

            $attributeType = new ObjectType([
                'name' => 'AttributeSet',
                'fields' => [
                    'id' => Type::nonNull(Type::string()),
                    'name' => Type::nonNull(Type::string()),
                    'type' => Type::nonNull(Type::string()),
                    'items' => Type::nonNull(Type::listOf(Type::nonNull($attributeItemType))),
                ],
            ]);

            $productType = null;
            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => static fn (): array => [
                    'id' => Type::nonNull(Type::string()),
                    'slug' => Type::nonNull(Type::string()),
                    'type' => Type::nonNull(Type::string()),
                    'name' => Type::nonNull(Type::string()),
                    'description' => Type::string(),
                    'inStock' => Type::nonNull(Type::boolean()),
                    'category' => Type::nonNull(Type::string()),
                    'brand' => Type::nonNull(Type::string()),
                    'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                    'prices' => Type::nonNull(Type::listOf(Type::nonNull($priceType))),
                    'attributes' => Type::nonNull(Type::listOf(Type::nonNull($attributeType))),
                ],
            ]);

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

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::nonNull(Type::listOf(Type::nonNull($productType))),
                        'args' => [
                            'category' => ['type' => Type::string()],
                        ],
                        'resolve' => static fn ($rootValue, array $args) => $productService->listProducts($args['category'] ?? null),
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'slug' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => static fn ($rootValue, array $args) => $productService->getProduct($args['slug']),
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
                        'resolve' => static fn ($rootValue, array $args, array $context) => $productService->createProduct(
                            $args['input'],
                            $context['uploadedFiles'] ?? [],
                        ),
                    ],
                ],
            ]);
        
            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
            );
        
            $input = self::parseInput();
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
            $context = [
                'uploadedFiles' => self::normalizeUploadedFiles($_FILES['galleryFiles'] ?? []),
            ];
        
            $result = GraphQLBase::executeQuery($schema, $query, null, $context, $variableValues);
            $debugFlags = ($_ENV['APP_ENV'] ?? 'dev') === 'prod'
                ? DebugFlag::NONE
                : DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
            $output = $result->toArray($debugFlags);
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }

    private static function parseInput(): array
    {
        if (str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data')) {
            $operations = $_POST['operations'] ?? null;

            if (! is_string($operations)) {
                throw new RuntimeException('Missing GraphQL operations payload.');
            }

            $decoded = json_decode($operations, true);

            if (! is_array($decoded)) {
                throw new RuntimeException('Invalid GraphQL operations payload.');
            }

            return $decoded;
        }

        $rawInput = file_get_contents('php://input');

        if ($rawInput === false) {
            throw new RuntimeException('Failed to get php://input');
        }

        $decoded = json_decode($rawInput, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Invalid GraphQL request payload.');
        }

        return $decoded;
    }

    private static function normalizeUploadedFiles(array $uploadedFiles): array
    {
        if ($uploadedFiles === [] || ! isset($uploadedFiles['name'])) {
            return [];
        }

        if (! is_array($uploadedFiles['name'])) {
            return [$uploadedFiles];
        }

        $normalized = [];
        $count = count($uploadedFiles['name']);

        for ($index = 0; $index < $count; $index++) {
            $normalized[] = [
                'name' => $uploadedFiles['name'][$index],
                'type' => $uploadedFiles['type'][$index],
                'tmp_name' => $uploadedFiles['tmp_name'][$index],
                'error' => $uploadedFiles['error'][$index],
                'size' => $uploadedFiles['size'][$index],
            ];
        }

        return $normalized;
    }
}
