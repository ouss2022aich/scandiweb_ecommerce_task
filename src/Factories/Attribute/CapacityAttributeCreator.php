<?php

declare(strict_types=1);

namespace App\Factories\Attribute;

use App\Models\Attribute;
use App\Models\CapacityAttribute;

class CapacityAttributeCreator implements AttributeCreatorInterface
{
    public function supports(string $name, string $type): bool
    {
        return $name === 'Capacity';
    }

    public function create(string $name): Attribute
    {
        return new CapacityAttribute($name);
    }
}
