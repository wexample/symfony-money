<?php

namespace Wexample\SymfonyMoney\Interface;

/**
 * A priced line that belongs to a priced parent (cart item, invoice item).
 */
interface PricedChildInterface extends PricedInterface
{
    public function getPriceParent(): ?PricedParentInterface;
}
