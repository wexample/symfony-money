<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyMoney\Enum\PriceUnit;
use Wexample\SymfonyMoney\Helper\RateHelper;

/**
 * A fee taken on the net price (excluding VAT), e.g. a platform commission.
 * It never changes the price itself: it is read by whoever bills the fee.
 */
trait HasPriceFeeTrait
{
    #[Column(type: Types::INTEGER)]
    protected int $priceFee = 0;

    #[Column(type: Types::STRING, length: 10, enumType: PriceUnit::class)]
    protected PriceUnit $priceFeeUnit = PriceUnit::Percent;

    public function getPriceFee(): int
    {
        return $this->priceFee;
    }

    public function getPriceFeeUnit(): PriceUnit
    {
        return $this->priceFeeUnit;
    }

    public function setPriceFee(
        int $priceFee,
        PriceUnit $unit = PriceUnit::Percent
    ): static {
        $this->priceFee = $priceFee;
        $this->priceFeeUnit = $unit;

        return $this;
    }

    public function calcPriceFee(): int
    {
        if (PriceUnit::Percent === $this->priceFeeUnit) {
            return RateHelper::rateOf($this->calcPriceNet(), $this->priceFee);
        }

        return $this->priceFee;
    }
}
