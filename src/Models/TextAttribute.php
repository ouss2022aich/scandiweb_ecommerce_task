<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TextAttribute extends Attribute
{
    public function getType(): string
    {
        return 'text';
    }
}
