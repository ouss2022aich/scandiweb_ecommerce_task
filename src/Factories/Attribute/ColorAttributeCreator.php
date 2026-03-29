<?php

declare(strict_types=1);

namespace App\Factories\Attribute;

use App\Models\Attribute;
use App\Models\ColorAttribute;

class ColorAttributeCreator implements AttributeCreatorInterface
{
    public function supports(string $name, string $type): bool
    {
        return $name === 'Color' && $type === 'swatch';
    }

    public function create(string $name): Attribute
    {
        return new ColorAttribute($name);
    }
}
