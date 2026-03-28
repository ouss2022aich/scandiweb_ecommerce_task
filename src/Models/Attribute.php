<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'attributes')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'size' => SizeAttribute::class,
    'color' => ColorAttribute::class,
    'capacity' => CapacityAttribute::class,
    'text' => TextAttribute::class,
])]
abstract class Attribute
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    protected string $name;

    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: AttributeItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $items;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(AttributeItem $item): void
    {
        if ($this->items->contains($item)) {
            return;
        }

        $this->items->add($item);
        $item->setAttribute($this);
    }

    abstract public function getType(): string;
}
