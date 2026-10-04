<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyMoney\Class\PriceBreakdown;

/**
 * Storage and accessors shared by every priced entity. Amounts are minor units.
 *
 * `priceTotal` is a stored copy of the computed total, refreshed by updatePriceTotal()
 * so that it can be queried; it never holds the override. `priceOverridden`, when set,
 * replaces the total in calcPriceFinal().
 *
 * The using class decides how the breakdown is computed: see PricedSingleTrait,
 * PricedChildTrait and PricedParentTrait.
 */
trait PricedTrait
{
    #[Column(type: Types::INTEGER, nullable: true)]
    protected ?int $priceRaw = null;

    #[Column(type: Types::INTEGER, nullable: true)]
    protected ?int $priceTotal = null;

    #[Column(type: Types::INTEGER, nullable: true)]
    protected ?int $priceOverridden = null;

    abstract public function calcPriceBreakdown(): PriceBreakdown;

    public function getPriceRaw(): ?int
    {
        return $this->priceRaw;
    }

    public function setPriceRaw(?int $priceRaw): static
    {
        $this->priceRaw = $priceRaw;

        return $this->updatePriceTotal();
    }

    public function getPriceTotal(): ?int
    {
        return $this->priceTotal;
    }

    public function getPriceOverridden(): ?int
    {
        return $this->priceOverridden;
    }

    public function setPriceOverridden(?int $priceOverridden): static
    {
        $this->priceOverridden = $priceOverridden;

        return $this->updatePriceTotal();
    }

    public function hasPriceOverridden(): bool
    {
        return null !== $this->priceOverridden;
    }

    public function calcPriceSubTotal(): int
    {
        return $this->calcPriceBreakdown()->subTotal;
    }

    public function calcPriceNet(): int
    {
        return $this->calcPriceBreakdown()->getNet();
    }

    public function calcPriceVat(): int
    {
        return $this->calcPriceBreakdown()->getVat();
    }

    public function calcPriceTotal(): int
    {
        return $this->calcPriceBreakdown()->getTotal();
    }

    public function calcPriceFinal(): int
    {
        return $this->calcPriceBreakdown()->getFinal();
    }

    public function updatePriceTotal(): static
    {
        $this->priceTotal = $this->calcPriceTotal();

        return $this;
    }
}
