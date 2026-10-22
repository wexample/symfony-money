<?php

namespace Wexample\SymfonyMoney\Interface;

/**
 * A priced line multiplied by a quantity.
 *
 * The quantity is an int scaled by getQuantityScale(): with a scale of 100,
 * 150 means 1.5. A scale of 1 counts plain units.
 */
interface QuantifiedInterface
{
    public function getQuantity(): ?int;

    public function getQuantityScale(): int;
}
