<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Wexample\SymfonyMoney\Class\PriceBreakdown;
use Wexample\SymfonyMoney\Class\VatLine;
use Wexample\SymfonyMoney\Helper\PriceCalculatorHelper;

/**
 * A priced document summing its children, with one VAT line per rate.
 * The using class implements PricedParentInterface.
 */
trait PricedParentTrait
{
    use PricedTrait;

    public function calcPriceBreakdown(): PriceBreakdown
    {
        return PriceCalculatorHelper::parent($this);
    }

    /**
     * @return array<int, VatLine>
     */
    public function calcVatLines(): array
    {
        return $this->calcPriceBreakdown()->vatLines;
    }
}
