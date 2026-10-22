<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

/**
 * VAT rate in basis points (2000 = 20 %). Implements VatRatedInterface.
 */
trait HasPriceVatTrait
{
    #[Column(type: Types::INTEGER)]
    protected int $priceVat = 0;

    public function getPriceVat(): int
    {
        return $this->priceVat;
    }

    public function setPriceVat(int $priceVat): static
    {
        $this->priceVat = $priceVat;

        return $this->updatePriceTotal();
    }

    public function hasPriceVat(): bool
    {
        return 0 !== $this->priceVat;
    }
}
