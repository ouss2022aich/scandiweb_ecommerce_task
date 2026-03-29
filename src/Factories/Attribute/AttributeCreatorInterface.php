<?php

declare(strict_types=1);

namespace App\Factories\Attribute;

use App\Models\Attribute;

interface AttributeCreatorInterface
{
    public function supports(string $name, string $type): bool;

    public function create(string $name): Attribute;
}
