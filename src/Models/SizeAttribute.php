<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SizeAttribute extends Attribute
{
    protected function supportedCategoryCodes(): array
    {
        return ['tech', 'clothes'];
    }

    public function getType(): string
    {
        return 'text';
    }
}
