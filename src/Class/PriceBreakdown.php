<?php

namespace Wexample\SymfonyMoney\Class;

/**
 * Every intermediate amount of a price computation, in minor units.
 */
final readonly class PriceBreakdown
{
    /**
     * @param array<int, VatLine> $vatLines Keyed by VAT rate in basis points
     */
    public function __construct(
        public int $subTotal,
        public int $discount,
        public array $vatLines,
        public ?int $overridden = null,
    ) {
    }

    public function getNet(): int
    {
        return $this->subTotal - $this->discount;
    }

    public function getVat(): int
    {
        $vat = 0;

        foreach ($this->vatLines as $line) {
            $vat += $line->vat;
        }

        return $vat;
    }

    public function getTotal(): int
    {
        return $this->getNet() + $this->getVat();
    }

    public function getFinal(): int
    {
        return $this->overridden ?? $this->getTotal();
    }

    public function isOverridden(): bool
    {
        return null !== $this->overridden;
    }

    public function getVatLine(int $rate): ?VatLine
    {
        return $this->vatLines[$rate] ?? null;
    }
}
