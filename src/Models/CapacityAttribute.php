<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CapacityAttribute extends Attribute
{
    public function supportsTechProducts(): bool
    {
        return false;
    }

    public function supportsClothingProducts(): bool
    {
        return true;
    }
}