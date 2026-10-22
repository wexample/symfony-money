<?php

namespace Wexample\SymfonyMoney\Helper;

use Symfony\Component\Intl\Currencies;
use Wexample\SymfonyMoney\Constant\CryptoCurrencyConstant;

/**
 * Conversions between decimal amounts and minor units (cents for EUR, yen for JPY).
 */
class MoneyHelper
{
    public static function getDecimals(string $currencyCode): int
    {
        $currencyCode = strtoupper($currencyCode);
        $crypto = CryptoCurrencyConstant::DECIMALS[$currencyCode] ?? null;

        if (null !== $crypto) {
            return $crypto;
        }

        if (Currencies::exists($currencyCode)) {
            return Currencies::getFractionDigits($currencyCode);
        }

        return 2;
    }

    /**
     * "19.99" EUR → 1999, "1 234,56" EUR → 123456, "500" JPY → 500.
     * Rounds half away from zero on extra digits; never truncates.
     */
    public static function fromDecimal(
        string|int|float $amount,
        string $currencyCode
    ): int {
        $decimals = static::getDecimals($currencyCode);

        if (is_float($amount)) {
            $amount = number_format($amount, $decimals + 2, '.', '');
        }

        $normalized = static::normalizeDecimalString((string) $amount);

        $negative = str_starts_with($normalized, '-');
        $normalized = ltrim($normalized, '-+');
        [$integer, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');

        $integer = '' === $integer ? '0' : $integer;
        $kept = substr(str_pad($fraction, $decimals, '0'), 0, $decimals);
        $rest = substr($fraction, $decimals);

        $minor = (int) ($integer.$kept);

        if ('' !== $rest && (int) $rest[0] >= 5) {
            ++$minor;
        }

        return $negative ? -$minor : $minor;
    }

    /**
     * 1999 EUR → "19.99".
     */
    public static function toDecimal(
        int $minor,
        string $currencyCode
    ): string {
        $decimals = static::getDecimals($currencyCode);

        if (0 === $decimals) {
            return (string) $minor;
        }

        $sign = $minor < 0 ? '-' : '';
        $digits = str_pad((string) abs($minor), $decimals + 1, '0', STR_PAD_LEFT);

        return $sign.substr($digits, 0, -$decimals).'.'.substr($digits, -$decimals);
    }

    /**
     * Accepts "1234.56", "1234,56", "1 234,56", "1.234,56", "1,234.56", "-12,5".
     * When both "," and "." appear, the last one is the decimal separator. A single
     * "," or "." is decimal; a repeated one is a thousands separator.
     */
    public static function normalizeDecimalString(string $amount): string
    {
        $amount = trim($amount);
        $amount = preg_replace('/[\s\x{00A0}\x{202F}\'’]/u', '', $amount) ?? '';
        $amount = preg_replace('/[^0-9,.\-+]/', '', $amount) ?? '';

        $lastComma = strrpos($amount, ',');
        $lastDot = strrpos($amount, '.');

        if (false !== $lastComma && false !== $lastDot) {
            if ($lastComma > $lastDot) {
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            } else {
                $amount = str_replace(',', '', $amount);
            }
        } elseif (false !== $lastComma) {
            $amount = substr_count($amount, ',') > 1
                ? str_replace(',', '', $amount)
                : str_replace(',', '.', $amount);
        } elseif (false !== $lastDot && substr_count($amount, '.') > 1) {
            $amount = str_replace('.', '', $amount);
        }

        return '' === $amount ? '0' : $amount;
    }
}
