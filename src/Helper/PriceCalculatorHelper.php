<?php

namespace Wexample\SymfonyMoney\Helper;

use Wexample\SymfonyMoney\Class\PriceBreakdown;
use Wexample\SymfonyMoney\Class\VatLine;
use Wexample\SymfonyMoney\Enum\PriceUnit;
use Wexample\SymfonyMoney\Interface\DiscountedInterface;
use Wexample\SymfonyMoney\Interface\PricedInterface;
use Wexample\SymfonyMoney\Interface\PricedParentInterface;
use Wexample\SymfonyMoney\Interface\QuantifiedInterface;
use Wexample\SymfonyMoney\Interface\VatRatedInterface;

/**
 * The price rules, in one place, for every priced entity.
 *
 * A line: subtotal = raw × quantity; net = subtotal − discount; VAT on the net.
 * A parent: children's net prices are grouped by VAT rate; the parent discount is
 * spread over those bases in proportion (so it lowers each taxable base, as the law
 * expects); the VAT of each rate is computed once, on its base.
 */
class PriceCalculatorHelper
{
    public static function line(PricedInterface $line): PriceBreakdown
    {
        $raw = $line->getPriceRaw() ?? 0;
        $subTotal = $raw;

        if ($line instanceof QuantifiedInterface) {
            $subTotal = RateHelper::mulDiv(
                $raw,
                $line->getQuantity() ?? 0,
                $line->getQuantityScale()
            );
        }

        $discount = static::calcDiscount($line, $subTotal);
        $rate = $line instanceof VatRatedInterface ? $line->getPriceVat() : 0;
        $net = $subTotal - $discount;

        return new PriceBreakdown(
            subTotal: $subTotal,
            discount: $discount,
            vatLines: [$rate => new VatLine($rate, $net, RateHelper::rateOf($net, $rate))],
            overridden: $line->getPriceOverridden(),
        );
    }

    public static function parent(PricedParentInterface $parent): PriceBreakdown
    {
        $bases = [];

        foreach ($parent->getPricedChildren() as $child) {
            $childBreakdown = $child->calcPriceBreakdown();

            // An overridden single-rate child counts for the base its final price implies.
            if ($childBreakdown->isOverridden() && 1 === count($childBreakdown->vatLines)) {
                $rate = array_key_first($childBreakdown->vatLines);
                $bases[$rate] = ($bases[$rate] ?? 0)
                    + RateHelper::extractBase($childBreakdown->overridden, $rate);

                continue;
            }

            foreach ($childBreakdown->vatLines as $rate => $vatLine) {
                $bases[$rate] = ($bases[$rate] ?? 0) + $vatLine->base;
            }
        }

        ksort($bases);
        $subTotal = array_sum($bases);
        $discounts = static::spreadDiscount($parent, $bases);

        $vatLines = [];
        foreach ($bases as $rate => $base) {
            $net = $base - $discounts[$rate];
            $vatLines[$rate] = new VatLine($rate, $net, RateHelper::rateOf($net, $rate));
        }

        return new PriceBreakdown(
            subTotal: $subTotal,
            discount: array_sum($discounts),
            vatLines: $vatLines,
            overridden: $parent->getPriceOverridden(),
        );
    }

    public static function calcDiscount(
        object $priced,
        int $amount
    ): int {
        if (! $priced instanceof DiscountedInterface) {
            return 0;
        }

        $value = $priced->getPriceDiscount();

        if (! $value) {
            return 0;
        }

        if (PriceUnit::Percent === $priced->getPriceDiscountUnit()) {
            return RateHelper::rateOf($amount, $value);
        }

        return $value;
    }

    /**
     * @param array<int, int> $bases
     * @return array<int, int>
     */
    private static function spreadDiscount(
        object $parent,
        array $bases
    ): array {
        if (! $parent instanceof DiscountedInterface || ! $parent->getPriceDiscount()) {
            return array_map(fn () => 0, $bases);
        }

        if (PriceUnit::Percent === $parent->getPriceDiscountUnit()) {
            return array_map(
                fn (int $base) => RateHelper::rateOf($base, $parent->getPriceDiscount()),
                $bases
            );
        }

        return RateHelper::allocate($parent->getPriceDiscount(), $bases);
    }
}
