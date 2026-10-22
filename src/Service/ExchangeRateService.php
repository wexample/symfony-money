<?php

namespace Wexample\SymfonyMoney\Service;

use DateTimeImmutable;
use Wexample\SymfonyMoney\Class\ExchangeRate;
use Wexample\SymfonyMoney\Interface\ExchangeRateProviderInterface;

/**
 * Exchange rates through whichever provider the application installed. Without
 * one, only a currency against itself has a rate: amounts in other currencies
 * need a rate typed in.
 */
class ExchangeRateService
{
    public function __construct(
        private readonly ?ExchangeRateProviderInterface $provider = null,
    ) {
    }

    public function isAvailable(): bool
    {
        return null !== $this->provider;
    }

    public function getRate(
        string $from,
        string $to,
        DateTimeImmutable $date
    ): ?ExchangeRate {
        if (strtoupper($from) === strtoupper($to)) {
            return ExchangeRate::identity(strtoupper($from), $date);
        }

        return $this->provider?->getRate(strtoupper($from), strtoupper($to), $date);
    }
}
