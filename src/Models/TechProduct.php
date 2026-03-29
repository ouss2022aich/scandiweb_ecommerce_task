<?php

namespace App\Models;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TechProduct extends Product
{
    public const TYPE = 'tech';

    protected function supportedCategoryCodes(): array
    {
        return ['tech'];
    }
}
