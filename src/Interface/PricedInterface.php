<?php

namespace Wexample\SymfonyMoney\Interface;

use Wexample\SymfonyMoney\Class\PriceBreakdown;

/**
 * Something with a price in minor units.
 *
 * - raw: the unit price, excluding VAT;
 * - subtotal: raw × quantity, or the sum of the children's net prices;
 * - net: subtotal minus discount, still excluding VAT;
 * - total: net plus VAT;
 * - final: the overridden price when one is set, the total otherwise.
 */
interface PricedInterface
{
    public function getPriceRaw(): ?int;

    public function getPriceOverridden(): ?int;

    public function getPriceTotal(): ?int;

    public function calcPriceBreakdown(): PriceBreakdown;

    public function calcPriceFinal(): int;

    /**
     * Refresh the stored total. Called by every setter that changes a price.
     */
    public function updatePriceTotal(): static;
}
