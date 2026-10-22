<?php

namespace Wexample\SymfonyMoney\Service;

use Wexample\SymfonyMoney\Interface\VatRateResolverInterface;

/**
 * Every buyer pays the seller's own rate: what applies without a package knowing
 * the tax rules.
 */
class DefaultVatRateResolver implements VatRateResolverInterface
{
    public function resolveVatRate(
        int $rate,
        bool $goods,
        ?string $buyerCountryCode,
        ?string $buyerVatNumber
    ): int {
        return $rate;
    }
}
