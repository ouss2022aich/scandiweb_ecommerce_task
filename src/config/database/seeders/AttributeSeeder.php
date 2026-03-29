<?php

declare(strict_types=1);

namespace App\config\database\seeders;

use App\Factories\Attribute\AttributeFactory;
use App\Models\Attribute;
use App\Models\AttributeItem;
use Doctrine\ORM\EntityManager;

class AttributeSeeder
{
    public function __construct(
        private readonly AttributeFactory $attributeFactory = new AttributeFactory(),
    ) {
    }

    public function seed(EntityManager $entityManager, array $attributes): void
    {
        $attributeRepository = $entityManager->getRepository(Attribute::class);
        $attributeItemRepository = $entityManager->getRepository(AttributeItem::class);

        foreach ($attributes as $attributeData) {
            $attribute = $attributeRepository->findOneBy(['name' => $attributeData['name']]);

            if ($attribute === null) {
                $attribute = $this->attributeFactory->create($attributeData['name'], $attributeData['type']);
                $entityManager->persist($attribute);
                $entityManager->flush();
            }

            foreach ($attributeData['items'] as $itemData) {
                $item = $attributeItemRepository->findOneBy([
                    'attribute' => $attribute,
                    'value' => $itemData['value'],
                ]);

                if ($item !== null) {
                    continue;
                }

                $entityManager->persist(new AttributeItem(
                    $itemData['displayValue'],
                    $itemData['value'],
                    $attribute,
                ));
            }
        }
    }
}
