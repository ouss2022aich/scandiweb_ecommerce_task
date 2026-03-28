<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'currencies')]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 16, unique: true)]
    protected string $label;

    #[ORM\Column(type: 'string', length: 16)]
    protected string $symbol;

    #[ORM\OneToMany(mappedBy: 'currency', targetEntity: Price::class)]
    protected Collection $prices;

    public function __construct(string $label, string $symbol)
    {
        $this->label = $label;
        $this->symbol = $symbol;
        $this->prices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }
}
