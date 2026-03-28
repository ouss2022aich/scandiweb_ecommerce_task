<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "tech" => TechProduct::class,
    "clothes" => ClothesProduct::class
])]
abstract class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 191, unique: true)]
    protected string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    protected bool $inStock = true;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'category_name', referencedColumnName: 'name', nullable: false)]
    protected Category $category;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    protected Brand $brand;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: GalleryItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $galleryItems;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Price::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $prices;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductAttributeItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected Collection $attributeItems;

    public function __construct(string $slug, string $name, Category $category, Brand $brand)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->galleryItems = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->attributeItems = new ArrayCollection();
        $this->setCategory($category);
        $this->setBrand($brand);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function setInStock(bool $inStock): void
    {
        $this->inStock = $inStock;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function getGalleryItems(): Collection
    {
        return $this->galleryItems;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function getAttributeItems(): Collection
    {
        return $this->attributeItems;
    }

    public function setCategory(Category $category): void
    {
        if (! $this->supportsCategory($category)) {
            throw new InvalidArgumentException(sprintf(
                'Category "%s" is not supported by product type "%s".',
                $category->getCode(),
                static::class
            ));
        }

        $this->category = $category;
        $category->addProduct($this);
    }

    public function setBrand(Brand $brand): void
    {
        $this->brand = $brand;
        $brand->addProduct($this);
    }

    public function supportsAttribute(Attribute $attribute): bool
    {
        return $this->category->supportsAttribute($attribute);
    }

    public function addGalleryItem(GalleryItem $galleryItem): void
    {
        if ($this->galleryItems->contains($galleryItem)) {
            return;
        }

        $this->galleryItems->add($galleryItem);
        $galleryItem->setProduct($this);
    }

    public function addPrice(Price $price): void
    {
        if ($this->prices->contains($price)) {
            return;
        }

        $this->prices->add($price);
        $price->setProduct($this);
    }

    public function addAttributeItem(ProductAttributeItem $productAttributeItem): void
    {
        if (! $this->supportsAttribute($productAttributeItem->getAttribute())) {
            throw new InvalidArgumentException(sprintf(
                'Attribute "%s" is not supported by category "%s".',
                $productAttributeItem->getAttribute()->getName(),
                $this->category->getCode()
            ));
        }

        if ($this->attributeItems->contains($productAttributeItem)) {
            return;
        }

        $this->attributeItems->add($productAttributeItem);
        $productAttributeItem->setProduct($this);
    }

    abstract protected function supportsCategory(Category $category): bool;
}
