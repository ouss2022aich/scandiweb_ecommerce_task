<?php

declare(strict_types=1);

namespace App\Factories\Attribute;

use App\Models\Attribute;
use App\Models\TextAttribute;

class TextAttributeCreator implements AttributeCreatorInterface
{
    public function supports(string $name, string $type): bool
    {
        return $type === 'text';
    }

    public function create(string $name): Attribute
    {
        return new TextAttribute($name);
    }
}
