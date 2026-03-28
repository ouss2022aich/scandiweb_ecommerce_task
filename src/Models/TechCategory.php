<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tech_categories')]
class TechCategory extends Category
{
    public function __construct()
    {
        parent::__construct('tech');
    }

    public function getCode(): string
    {
        return 'tech';
    }

    public function supportsAttribute(Attribute $attribute): bool
    {
        return $attribute instanceof ColorAttribute
            || $attribute instanceof SizeAttribute
            || $attribute instanceof CapacityAttribute
            || $attribute instanceof TextAttribute;
    }
}
