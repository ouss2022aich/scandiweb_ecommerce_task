<?php

namespace App\Models;

use App\Models\Product;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ClothingProduct extends Product
{
    public function supportsAttribute(Attribute $attribute): bool
    {
        return $attribute->supportsClothingProducts();
    }
}