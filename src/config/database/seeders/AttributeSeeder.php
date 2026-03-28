<?php

declare(strict_types=1);

namespace App\config\database\seeders;

use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\CapacityAttribute;
use App\Models\ColorAttribute;
use App\Models\SizeAttribute;
use App\Models\TextAttribute;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class AttributeSeeder
{
    public function seed(EntityManager $entityManager, array $attributes): void
    {
        $attributeRepository = $entityManager->getRepository(Attribute::class);
        $attributeItemRepository = $entityManager->getRepository(AttributeItem::class);

        foreach ($attributes as $attributeData) {
            $attribute = $attributeRepository->findOneBy(['name' => $attributeData['name']]);

            if ($attribute === null) {
                $attribute = $this->createAttribute($attributeData['name'], $attributeData['type']);
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

    private function createAttribute(string $name, string $type): Attribute
    {
        return match (true) {
            $name === 'Size' => new SizeAttribute($name),
            $name === 'Color' && $type === 'swatch' => new ColorAttribute($name),
            $name === 'Capacity' => new CapacityAttribute($name),
            $type === 'text' => new TextAttribute($name),
            default => throw new InvalidArgumentException(sprintf(
                'Unsupported attribute "%s" with type "%s".',
                $name,
                $type,
            )),
        };
    }
}
