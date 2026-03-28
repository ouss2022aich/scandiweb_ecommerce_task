<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ColorAttribute extends Attribute
{
    public function getType(): string
    {
        return 'swatch';
    }
}
