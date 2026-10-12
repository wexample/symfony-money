<?php

namespace Wexample\SymfonyMoney\Class;

use DateTimeImmutable;
use InvalidArgumentException;
use Wexample\SymfonyMoney\Helper\MoneyHelper;
use Wexample\SymfonyMoney\Helper\RateHelper;

/**
 * How many units of `to` one unit of `from` is worth on a day: 1 USD = 0.9221 EUR.
 * The rate is kept as a decimal string, read to six decimals; converting rounds
 * half away from zero in the target currency's minor unit, without floats.
 */
final readonly class ExchangeRate
{
    public const int SCALE = 1_000_000;

    public function __construct(
        public string $from,
        public string $to,
        public string $rate,
        public DateTimeImmutable $date,
        /** Who gave it: `ecb`, `manual`… */
        public string $source = 'manual',
    ) {
        if (! is_numeric($rate) || (float) $rate <= 0) {
            throw new InvalidArgumentException(sprintf('"%s" is no exchange rate.', $rate));
        }
    }

    public static function identity(
        string $currency,
        DateTimeImmutable $date
    ): self {
        return new self($currency, $currency, '1', $date, 'identity');
    }

    /**
     * The rate the other way round, to six decimals.
     */
    public function invert(): self
    {
        return new self($this->to, $this->from, self::format(RateHelper::mulDiv(self::SCALE, self::SCALE, $this->scaled())), $this->date, $this->source);
    }

    /**
     * An amount in `from` minor units, in `to` minor units.
     */
    public function convert(int $minor): int
    {
        $fromDecimals = MoneyHelper::getDecimals($this->from);
        $toDecimals = MoneyHelper::getDecimals($this->to);

        return RateHelper::mulDiv($minor, $this->scaled() * 10 ** $toDecimals, self::SCALE * 10 ** $fromDecimals);
    }

    /**
     * The rate times SCALE, read from its decimal string: a seventh decimal rounds.
     */
    public function scaled(): int
    {
        [$integer, $fraction] = array_pad(explode('.', ltrim($this->rate, '+'), 2), 2, '');
        $kept = substr(str_pad($fraction, 6, '0'), 0, 6);
        $scaled = (int) ($integer.$kept);

        return isset($fraction[6]) && (int) $fraction[6] >= 5 ? $scaled + 1 : $scaled;
    }

    public static function format(int $scaled): string
    {
        return rtrim(rtrim(number_format($scaled / self::SCALE, 6, '.', ''), '0'), '.');
    }
}
