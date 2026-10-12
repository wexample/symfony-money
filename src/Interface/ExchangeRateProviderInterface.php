<?php

namespace Wexample\SymfonyMoney\Interface;

use DateTimeImmutable;
use Wexample\SymfonyMoney\Class\ExchangeRate;

/**
 * Where exchange rates come from: a central bank, a market feed. Implemented by a
 * remote package (symfony-remote-ecb…); money only states the contract.
 */
interface ExchangeRateProviderInterface
{
    /**
     * The rate of the day, or of the last day before it the source published one
     * (no rate on weekends and holidays); null when the source has none.
     */
    public function getRate(
        string $from,
        string $to,
        DateTimeImmutable $date
    ): ?ExchangeRate;
}
