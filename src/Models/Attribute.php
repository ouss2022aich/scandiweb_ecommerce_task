<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'attributes')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'size' => SizeAttribute::class,
    'color' => ColorAttribute::class,
    'capacity' => CapacityAttribute::class,
])]
abstract class Attribute
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract public function supportsTechProducts(): bool;
    abstract public function supportsClothingProducts(): bool;
}