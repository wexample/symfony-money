<?php

namespace Wexample\SymfonyMoney\Interface;

use Wexample\SymfonyMoney\Enum\PriceUnit;

/**
 * A priced object with a discount applied on its subtotal, before VAT.
 */
interface DiscountedInterface
{
    public function getPriceDiscount(): ?int;

    public function getPriceDiscountUnit(): ?PriceUnit;
}
