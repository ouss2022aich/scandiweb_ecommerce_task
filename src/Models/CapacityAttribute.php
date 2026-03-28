<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CapacityAttribute extends Attribute
{
    public function getType(): string
    {
        return 'text';
    }
}
