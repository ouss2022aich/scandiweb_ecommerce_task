<?php

declare(strict_types=1);

namespace App\Seed;

use RuntimeException;

class JsonSeedLoader
{
    public function load(string $path): array
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
}
