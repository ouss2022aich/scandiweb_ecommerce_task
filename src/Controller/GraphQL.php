<?php

declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\SchemaFactory;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Error\DebugFlag;
use RuntimeException;
use Throwable;

class GraphQL
{
    public function __construct(
        private readonly SchemaFactory $schemaFactory,
    ) {
    }

    public function handle(array $vars = []): string
    {
        try {
            $schema = $this->schemaFactory->create();
            $input = $this->parseInput();
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
            $context = [
                'uploadedFiles' => $this->normalizeUploadedFiles($_FILES['galleryFiles'] ?? []),
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

    private function parseInput(): array
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

    private function normalizeUploadedFiles(array $uploadedFiles): array
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
