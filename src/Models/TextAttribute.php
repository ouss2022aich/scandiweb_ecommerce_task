<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TextAttribute extends Attribute
{
    protected function supportedCategoryCodes(): array
    {
        return ['tech'];
    }

    public function getType(): string
    {
        return 'text';
    }
}
