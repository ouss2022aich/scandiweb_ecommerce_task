<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'all' => AllCategory::class,
    'tech' => TechCategory::class,
    'clothes' => ClothesCategory::class,
])]
abstract class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 100)]
    protected string $name;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    protected Collection $products;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->products = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): void
    {
        if ($this->products->contains($product)) {
            return;
        }

        $this->products->add($product);

        if ($product->getCategory() !== $this) {
            $product->setCategory($this);
        }
    }

    public function supportsAttribute(Attribute $attribute): bool
    {
        return $attribute->supportsCategory($this);
    }

    abstract public function getCode(): string;
}
