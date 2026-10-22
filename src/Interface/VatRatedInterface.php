<?php

namespace Wexample\SymfonyMoney\Interface;

/**
 * A priced line carrying a VAT rate in basis points (2000 = 20 %).
 */
interface VatRatedInterface
{
    public function getPriceVat(): int;
}
