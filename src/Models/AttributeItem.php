<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'attribute_items',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_attribute_value', columns: ['attribute_id', 'value']),
    ]
)]
class AttributeItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $displayValue;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $value;

    #[ORM\ManyToOne(targetEntity: Attribute::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    protected Attribute $attribute;

    #[ORM\OneToMany(mappedBy: 'attributeItem', targetEntity: ProductAttributeItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $productItems;

    public function __construct(string $displayValue, string $value, Attribute $attribute)
    {
        $this->displayValue = $displayValue;
        $this->value = $value;
        $this->productItems = new ArrayCollection();
        $attribute->addItem($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisplayValue(): string
    {
        return $this->displayValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getProductItems(): Collection
    {
        return $this->productItems;
    }
}
