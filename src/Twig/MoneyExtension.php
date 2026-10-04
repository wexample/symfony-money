<?php

namespace Wexample\SymfonyMoney\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Wexample\SymfonyMoney\Service\MoneyFormatter;

class MoneyExtension extends AbstractExtension
{
    public function __construct(
        private readonly MoneyFormatter $moneyFormatter,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this->moneyFormatter, 'format']),
            new TwigFilter('price_number', [$this->moneyFormatter, 'formatNumber']),
            new TwigFilter('rate', [$this->moneyFormatter, 'formatRate']),
        ];
    }
}
