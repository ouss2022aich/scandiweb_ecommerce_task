<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'product_prices',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_product_currency_price', columns: ['product_id', 'currency_id']),
    ]
)]
class Price
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    protected string $amount;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    protected Product $product;

    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    protected Currency $currency;

    public function __construct(string $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
