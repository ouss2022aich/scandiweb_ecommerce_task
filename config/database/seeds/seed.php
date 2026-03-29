<?php

declare(strict_types=1);

use App\Seed\SeedRunner;

$entityManager = require __DIR__ . '/../doctrine.php';
(new SeedRunner($entityManager))->run(__DIR__);

echo "Initial reference data seeded.\n";
