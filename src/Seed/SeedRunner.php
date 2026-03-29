<?php

declare(strict_types=1);

namespace App\Seed;

use App\config\database\seeders\DatabaseSeeder;
use Doctrine\ORM\EntityManager;

class SeedRunner
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly DatabaseSeeder $databaseSeeder = new DatabaseSeeder(),
        private readonly JsonSeedLoader $jsonSeedLoader = new JsonSeedLoader(),
    ) {
    }

    public function run(string $seedDirectory): void
    {
        $seedData = [
            'categories' => $this->jsonSeedLoader->load($seedDirectory . '/categories.json'),
            'brands' => $this->jsonSeedLoader->load($seedDirectory . '/brands.json'),
            'currencies' => $this->jsonSeedLoader->load($seedDirectory . '/currencies.json'),
            'attributes' => $this->jsonSeedLoader->load($seedDirectory . '/attributes.json'),
        ];

        $this->databaseSeeder->seed($this->entityManager, $seedData);
    }
}
