<?php

declare(strict_types=1);

namespace App\Factories\Attribute;

use App\Models\Attribute;
use App\Models\SizeAttribute;

class SizeAttributeCreator implements AttributeCreatorInterface
{
    public function supports(string $name, string $type): bool
    {
        return $name === 'Size';
    }

    public function create(string $name): Attribute
    {
        return new SizeAttribute($name);
    }
}
