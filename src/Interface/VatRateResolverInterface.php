<?php

namespace Wexample\SymfonyMoney\Interface;

/**
 * The VAT rate a line is charged at, once the buyer is known. The default one
 * keeps the seller's own rate; a package knowing the tax rules (symfony-accounting)
 * takes its place, so that a business buyer from another EU country is charged none
 * (reverse charge) and a consumer abroad may be charged their country's rate (OSS).
 */
interface VatRateResolverInterface
{
    /**
     * @param int         $rate             The seller's own rate for the line, in basis points.
     * @param bool        $goods            Goods rather than services: their rules differ across borders.
     * @param string|null $buyerCountryCode ISO 3166-1 alpha-2; null when not known yet.
     * @param string|null $buyerVatNumber   With its country prefix, as the buyer typed it.
     *
     * @return int The rate to charge, in basis points.
     */
    public function resolveVatRate(
        int $rate,
        bool $goods,
        ?string $buyerCountryCode,
        ?string $buyerVatNumber
    ): int;
}
