<?php

namespace Wexample\SymfonyMoney\Class;

/**
 * The taxable base and the VAT of one rate, in minor units.
 */
final readonly class VatLine
{
    public function __construct(
        public int $rate,
        public int $base,
        public int $vat,
    ) {
    }

    public function getTotal(): int
    {
        return $this->base + $this->vat;
    }
}
