<?php

namespace Wexample\SymfonyMoney\Tests\Unit\Fixtures;

use Wexample\SymfonyMoney\Entity\Traits\HasPriceVatTrait;
use Wexample\SymfonyMoney\Entity\Traits\PricedSingleTrait;
use Wexample\SymfonyMoney\Interface\PricedInterface;
use Wexample\SymfonyMoney\Interface\VatRatedInterface;

class TestProduct implements PricedInterface, VatRatedInterface
{
    use PricedSingleTrait;
    use HasPriceVatTrait;
}
