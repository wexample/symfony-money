<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Wexample\SymfonyMoney\Class\PriceBreakdown;
use Wexample\SymfonyMoney\Helper\PriceCalculatorHelper;

/**
 * A priced line of a parent: any change refreshes the parent total too.
 * The using class implements PricedChildInterface.
 */
trait PricedChildTrait
{
    use PricedTrait {
        updatePriceTotal as private updateOwnPriceTotal;
    }

    public function calcPriceBreakdown(): PriceBreakdown
    {
        return PriceCalculatorHelper::line($this);
    }

    public function updatePriceTotal(): static
    {
        $this->updateOwnPriceTotal();
        $this->getPriceParent()?->updatePriceTotal();

        return $this;
    }
}
