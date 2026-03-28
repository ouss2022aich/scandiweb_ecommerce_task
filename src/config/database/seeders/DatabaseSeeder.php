<?php

declare(strict_types=1);

namespace App\config\database\seeders;

use Doctrine\ORM\EntityManager;

class DatabaseSeeder
{
    public function __construct(
        private readonly CategorySeeder $categorySeeder = new CategorySeeder(),
        private readonly BrandSeeder $brandSeeder = new BrandSeeder(),
        private readonly CurrencySeeder $currencySeeder = new CurrencySeeder(),
        private readonly AttributeSeeder $attributeSeeder = new AttributeSeeder(),
    ) {
    }

    public function seed(EntityManager $entityManager, array $seedData): void
    {
        $this->categorySeeder->seed($entityManager, $seedData['categories']);
        $this->brandSeeder->seed($entityManager, $seedData['brands']);
        $this->currencySeeder->seed($entityManager, $seedData['currencies']);
        $this->attributeSeeder->seed($entityManager, $seedData['attributes']);

        $entityManager->flush();
    }
}
