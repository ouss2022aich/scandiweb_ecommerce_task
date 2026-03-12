<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'tech' => TechCategory::class,
    'clothing' => ClothingCategory::class,
])]
abstract class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 100)]
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Used by compatibility policy or product validation.
     */
    abstract public function getCategoryCode(): string;
}