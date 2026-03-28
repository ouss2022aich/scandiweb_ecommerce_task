<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'clothes_categories')]
class ClothesCategory extends Category
{
    public function __construct()
    {
        parent::__construct('clothes');
    }

    public function getCode(): string
    {
        return 'clothes';
    }

    public function supportsAttribute(Attribute $attribute): bool
    {
        return $attribute instanceof ColorAttribute
            || $attribute instanceof SizeAttribute;
    }
}
