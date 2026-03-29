<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    protected Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected Product $product;

    #[ORM\Column(type: 'integer')]
    protected int $quantity;

    #[ORM\Column(type: 'json')]
    protected array $selectedAttributeItemIds = [];

    public function __construct(Product $product, int $quantity, array $selectedAttributeItemIds = [])
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->selectedAttributeItemIds = $selectedAttributeItemIds;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSelectedAttributeItemIds(): array
    {
        return $this->selectedAttributeItemIds;
    }
}
