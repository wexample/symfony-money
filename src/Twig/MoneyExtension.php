<?php

namespace Wexample\SymfonyMoney\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Wexample\SymfonyMoney\Service\MoneyFormatter;

class MoneyExtension extends AbstractExtension
{
    public function __construct(
        private readonly MoneyFormatter $moneyFormatter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this->moneyFormatter, 'format']),
            new TwigFilter('price_number', [$this->moneyFormatter, 'formatNumber']),
            new TwigFilter('price_excl_tax', [$this, 'priceExclTax']),
            new TwigFilter('price_incl_tax', [$this, 'priceInclTax']),
            new TwigFilter('rate', [$this->moneyFormatter, 'formatRate']),
        ];
    }

    /**
     * 123456 → "1 234,56 € HT" (fr), "€1,234.56 excl. VAT" (en), "1 234,56 € HTVA" (fr_BE).
     */
    public function priceExclTax(
        int $minor,
        ?string $currencyCode = null,
        ?string $locale = null
    ): string {
        return $this->withTaxSuffix($minor, 'excluded', $currencyCode, $locale);
    }

    public function priceInclTax(
        int $minor,
        ?string $currencyCode = null,
        ?string $locale = null
    ): string {
        return $this->withTaxSuffix($minor, 'included', $currencyCode, $locale);
    }

    private function withTaxSuffix(
        int $minor,
        string $basis,
        ?string $currencyCode,
        ?string $locale
    ): string {
        return $this->moneyFormatter->format($minor, $currencyCode, $locale)
            .' '.$this->translator->trans('price.tax.'.$basis, [], 'money', $locale);
    }
}
