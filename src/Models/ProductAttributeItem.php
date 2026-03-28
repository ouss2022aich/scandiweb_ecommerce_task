<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'product_items',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_product_attribute_item', columns: ['product_id', 'attributeItem_id']),
    ]
)]
class ProductAttributeItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'attributeItems')]
    #[ORM\JoinColumn(nullable: false)]
    protected Product $product;

    #[ORM\ManyToOne(targetEntity: AttributeItem::class, inversedBy: 'productItems')]
    #[ORM\JoinColumn(nullable: false)]
    protected AttributeItem $attributeItem;

    public function __construct(AttributeItem $attributeItem)
    {
        $this->attributeItem = $attributeItem;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getAttributeItem(): AttributeItem
    {
        return $this->attributeItem;
    }

    public function getAttribute(): Attribute
    {
        return $this->attributeItem->getAttribute();
    }
}
