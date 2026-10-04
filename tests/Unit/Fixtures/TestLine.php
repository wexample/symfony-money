<?php

namespace Wexample\SymfonyMoney\Tests\Unit\Fixtures;

use Wexample\SymfonyMoney\Entity\Traits\HasPriceDiscountTrait;
use Wexample\SymfonyMoney\Entity\Traits\HasPriceVatTrait;
use Wexample\SymfonyMoney\Entity\Traits\HasQuantityTrait;
use Wexample\SymfonyMoney\Entity\Traits\PricedChildTrait;
use Wexample\SymfonyMoney\Interface\DiscountedInterface;
use Wexample\SymfonyMoney\Interface\PricedChildInterface;
use Wexample\SymfonyMoney\Interface\PricedParentInterface;
use Wexample\SymfonyMoney\Interface\QuantifiedInterface;
use Wexample\SymfonyMoney\Interface\VatRatedInterface;

class TestLine implements PricedChildInterface, QuantifiedInterface, VatRatedInterface, DiscountedInterface
{
    use PricedChildTrait;
    use HasQuantityTrait;
    use HasPriceVatTrait;
    use HasPriceDiscountTrait;

    private ?TestDocument $document = null;

    public function __construct(private readonly int $quantityScale = 1)
    {
        $this->quantity = $quantityScale;
    }

    public function setDocument(TestDocument $document): void
    {
        $this->document = $document;
        $this->updatePriceTotal();
    }

    public function getPriceParent(): ?PricedParentInterface
    {
        return $this->document;
    }

    public function getQuantityScale(): int
    {
        return $this->quantityScale;
    }
}
