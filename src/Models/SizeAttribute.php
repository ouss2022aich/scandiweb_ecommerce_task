<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SizeAttribute extends Attribute
{
    public function getType(): string
    {
        return 'text';
    }
}
