<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ColorAttribute extends Attribute
{
    protected function supportedCategoryCodes(): array
    {
        return ['tech', 'clothes'];
    }

    public function getType(): string
    {
        return 'swatch';
    }
}
