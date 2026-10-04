<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Wexample\SymfonyMoney\Class\PriceBreakdown;
use Wexample\SymfonyMoney\Helper\PriceCalculatorHelper;

/**
 * A priced entity standing alone, e.g. a product.
 */
trait PricedSingleTrait
{
    use PricedTrait;

    public function calcPriceBreakdown(): PriceBreakdown
    {
        return PriceCalculatorHelper::line($this);
    }
}
