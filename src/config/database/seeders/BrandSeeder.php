<?php

declare(strict_types=1);

namespace App\config\database\seeders;

use App\Models\Brand;
use Doctrine\ORM\EntityManager;

class BrandSeeder
{
    public function seed(EntityManager $entityManager, array $brands): void
    {
        $repository = $entityManager->getRepository(Brand::class);

        foreach ($brands as $brandName) {
            if ($repository->findOneBy(['name' => $brandName]) !== null) {
                continue;
            }

            $entityManager->persist(new Brand($brandName));
        }
    }
}
