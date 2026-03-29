<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ClothesProduct extends Product
{
    public const TYPE = 'clothes';

    protected function supportedCategoryCodes(): array
    {
        return ['clothes'];
    }
}
