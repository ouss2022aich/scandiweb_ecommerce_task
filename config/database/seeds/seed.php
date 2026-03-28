<?php

declare(strict_types=1);

use App\config\database\seeders\DatabaseSeeder;

$entityManager = require __DIR__ . '/../doctrine.php';
$seedData = [
    'categories' => loadJsonSeedFile(__DIR__ . '/categories.json'),
    'brands' => loadJsonSeedFile(__DIR__ . '/brands.json'),
    'currencies' => loadJsonSeedFile(__DIR__ . '/currencies.json'),
    'attributes' => loadJsonSeedFile(__DIR__ . '/attributes.json'),
];

(new DatabaseSeeder())->seed($entityManager, $seedData);

echo "Initial reference data seeded.\n";

function loadJsonSeedFile(string $path): array
{
    $contents = file_get_contents($path);

    if ($contents === false) {
        throw new RuntimeException(sprintf('Unable to read seed file "%s".', $path));
    }

    $decoded = json_decode($contents, true);

    if (! is_array($decoded)) {
        throw new RuntimeException(sprintf('Invalid JSON in seed file "%s".', $path));
    }

    return $decoded;
}
