<?php

declare(strict_types=1);

namespace App\config\database\seeders;

use App\Models\Currency;
use Doctrine\ORM\EntityManager;

class CurrencySeeder
{
    public function seed(EntityManager $entityManager, array $currencies): void
    {
        $repository = $entityManager->getRepository(Currency::class);

        foreach ($currencies as $currencyData) {
            if ($repository->findOneBy(['label' => $currencyData['label']]) !== null) {
                continue;
            }

            $entityManager->persist(new Currency($currencyData['label'], $currencyData['symbol']));
        }
    }
}
