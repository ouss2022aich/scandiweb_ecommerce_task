<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'all_categories')]
class AllCategory extends Category
{
    public function __construct()
    {
        parent::__construct('all');
    }

    public function getCode(): string
    {
        return 'all';
    }
}
