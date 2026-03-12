<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'clothing_categories')]
class ClothingCategory extends Category
{
    public function __construct()
    {
        parent::__construct('clothes');
    }

    public function getCategoryCode(): string
    {
        return 'clothes';
    }
}