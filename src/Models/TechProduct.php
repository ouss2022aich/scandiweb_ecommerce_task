<?php

namespace App\Models;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TechProduct extends Product
{
    public function supportsAttribute(Attribute $attribute): bool
    {
        return $attribute->supportsTechProducts();
    }
}