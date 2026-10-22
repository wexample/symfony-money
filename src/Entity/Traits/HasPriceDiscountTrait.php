<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyMoney\Enum\PriceUnit;

/**
 * A discount on the subtotal, before VAT: an amount, or a rate in basis points.
 * Implements DiscountedInterface.
 */
trait HasPriceDiscountTrait
{
    #[Column(type: Types::INTEGER, nullable: true)]
    protected ?int $priceDiscount = null;

    #[Column(type: Types::STRING, length: 10, nullable: true, enumType: PriceUnit::class)]
    protected ?PriceUnit $priceDiscountUnit = null;

    public function getPriceDiscount(): ?int
    {
        return $this->priceDiscount;
    }

    public function getPriceDiscountUnit(): ?PriceUnit
    {
        return $this->priceDiscountUnit;
    }

    public function setPriceDiscount(
        ?int $priceDiscount,
        PriceUnit $unit = PriceUnit::Money
    ): static {
        $this->priceDiscount = $priceDiscount;
        $this->priceDiscountUnit = null === $priceDiscount ? null : $unit;

        return $this->updatePriceTotal();
    }

    public function hasPriceDiscount(): bool
    {
        return (bool) $this->priceDiscount;
    }
}
