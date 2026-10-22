<?php

namespace Wexample\SymfonyMoney\Interface;

/**
 * A line as a document prints and books it (cart item, invoice item): what was
 * sold, how many, at which rate, and the revenue account it goes to. Enough for a
 * package that never saw the line's own class to copy it onto another document.
 */
interface PricedLineInterface extends PricedChildInterface, QuantifiedInterface, VatRatedInterface
{
    public function getTitle(): string;

    /**
     * Goods rather than services: their VAT rules differ across borders.
     */
    public function isGoods(): bool;

    /**
     * The revenue account to book it on, when it differs from the default.
     */
    public function getAccountNumber(): ?string;
}
