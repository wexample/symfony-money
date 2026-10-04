<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

/**
 * The ISO 4217 code (or crypto code) a priced document is expressed in.
 * Unlike HasCurrencyCodeTrait, which identifies a Currency row, it is not unique.
 */
trait HasPriceCurrencyTrait
{
    #[Column(type: Types::STRING, length: 10)]
    protected string $currencyCode = 'EUR';

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): static
    {
        $this->currencyCode = strtoupper($currencyCode);

        return $this;
    }
}
