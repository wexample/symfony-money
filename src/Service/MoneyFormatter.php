<?php

namespace Wexample\SymfonyMoney\Service;

use NumberFormatter;
use Wexample\SymfonyMoney\Helper\MoneyHelper;
use Wexample\SymfonyMoney\Helper\RateHelper;

/**
 * Locale-aware display of minor-unit amounts and basis-point rates.
 * Entities never format money themselves: they go through this service or its Twig filters.
 */
class MoneyFormatter
{
    public function __construct(
        private readonly string $defaultLocale = 'fr',
        private readonly string $defaultCurrency = 'EUR',
    ) {
    }

    public function format(
        int $minor,
        ?string $currencyCode = null,
        ?string $locale = null
    ): string {
        $currencyCode ??= $this->defaultCurrency;
        $formatter = new NumberFormatter($locale ?? $this->defaultLocale, NumberFormatter::CURRENCY);
        $decimals = MoneyHelper::getDecimals($currencyCode);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);

        $formatted = $formatter->formatCurrency(
            (float) MoneyHelper::toDecimal($minor, $currencyCode),
            $currencyCode
        );

        return false === $formatted
            ? MoneyHelper::toDecimal($minor, $currencyCode).' '.$currencyCode
            : $formatted;
    }

    /**
     * The amount without currency sign: 123456 → "1 234,56" (fr).
     */
    public function formatNumber(
        int $minor,
        ?string $currencyCode = null,
        ?string $locale = null
    ): string {
        $currencyCode ??= $this->defaultCurrency;
        $decimals = MoneyHelper::getDecimals($currencyCode);
        $formatter = new NumberFormatter($locale ?? $this->defaultLocale, NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return (string) $formatter->format((float) MoneyHelper::toDecimal($minor, $currencyCode));
    }

    /**
     * 2000 → "20 %", 550 → "5,5 %" (fr).
     */
    public function formatRate(
        int $basisPoints,
        ?string $locale = null
    ): string {
        $formatter = new NumberFormatter($locale ?? $this->defaultLocale, NumberFormatter::PERCENT);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);

        return (string) $formatter->format($basisPoints / RateHelper::BASIS);
    }
}
