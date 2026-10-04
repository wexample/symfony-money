<?php

namespace Wexample\SymfonyMoney\Helper;

/**
 * Integer arithmetic on minor units and basis points.
 *
 * A rate is an int in basis points: 2000 = 20 %, 550 = 5.5 %.
 * Every result is rounded half away from zero, without going through floats.
 */
class RateHelper
{
    final public const BASIS = 10000;

    /**
     * round($a * $b / $divisor), half away from zero.
     */
    public static function mulDiv(
        int $a,
        int $b,
        int $divisor
    ): int {
        if (0 === $divisor) {
            throw new \DivisionByZeroError('Divisor cannot be zero.');
        }

        $product = $a * $b;
        $quotient = intdiv($product, $divisor);
        $remainder = $product % $divisor;

        if (abs($remainder) * 2 >= abs($divisor)) {
            $quotient += (($product < 0) xor ($divisor < 0)) ? -1 : 1;
        }

        return $quotient;
    }

    /**
     * The part of $amount that $rate represents: rateOf(10000, 2000) = 2000.
     */
    public static function rateOf(
        int $amount,
        int $rate
    ): int {
        return static::mulDiv($amount, $rate, self::BASIS);
    }

    /**
     * $amount increased by $rate: add(10000, 2000) = 12000.
     */
    public static function add(
        int $amount,
        int $rate
    ): int {
        return $amount + static::rateOf($amount, $rate);
    }

    /**
     * $amount decreased by $rate: subtract(10000, 1000) = 9000.
     */
    public static function subtract(
        int $amount,
        int $rate
    ): int {
        return $amount - static::rateOf($amount, $rate);
    }

    /**
     * The amount before $rate was added: extractBase(12000, 2000) = 10000.
     */
    public static function extractBase(
        int $amountIncludingRate,
        int $rate
    ): int {
        return static::mulDiv($amountIncludingRate, self::BASIS, self::BASIS + $rate);
    }

    /**
     * Split $amount across $weights proportionally, so that the parts always sum to $amount
     * (largest remainder method). Keys are preserved.
     *
     * @param array<array-key, int> $weights
     * @return array<array-key, int>
     */
    public static function allocate(
        int $amount,
        array $weights
    ): array {
        $totalWeight = array_sum($weights);
        $parts = [];

        if (0 === $totalWeight) {
            foreach ($weights as $key => $weight) {
                $parts[$key] = 0;
            }

            return $parts;
        }

        $remainders = [];
        $allocated = 0;

        foreach ($weights as $key => $weight) {
            $product = $amount * $weight;
            $parts[$key] = intdiv($product, $totalWeight);
            $remainders[$key] = abs($product % $totalWeight);
            $allocated += $parts[$key];
        }

        $left = $amount - $allocated;
        $step = $left < 0 ? -1 : 1;
        arsort($remainders);

        foreach (array_keys($remainders) as $key) {
            if (0 === $left) {
                break;
            }

            $parts[$key] += $step;
            $left -= $step;
        }

        return $parts;
    }
}
