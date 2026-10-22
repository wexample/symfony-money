<?php

namespace Wexample\SymfonyMoney\Interface;

/**
 * A priced document whose price is the sum of its children (cart, invoice).
 */
interface PricedParentInterface extends PricedInterface
{
    /**
     * @return iterable<PricedInterface>
     */
    public function getPricedChildren(): iterable;
}
