<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ClothesProduct extends Product
{
    protected function supportsCategory(Category $category): bool
    {
        return $category instanceof ClothesCategory
            || $category instanceof AllCategory;
    }
}
