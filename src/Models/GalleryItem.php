<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'gallery')]
class GalleryItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 2048)]
    protected string $imageUrl;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'galleryItems')]
    #[ORM\JoinColumn(nullable: false)]
    protected Product $product;

    public function __construct(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
}
