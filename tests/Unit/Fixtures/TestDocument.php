<?php

namespace Wexample\SymfonyMoney\Tests\Unit\Fixtures;

use Wexample\SymfonyMoney\Entity\Traits\HasPriceDiscountTrait;
use Wexample\SymfonyMoney\Entity\Traits\HasPriceFeeTrait;
use Wexample\SymfonyMoney\Entity\Traits\PricedParentTrait;
use Wexample\SymfonyMoney\Interface\DiscountedInterface;
use Wexample\SymfonyMoney\Interface\PricedParentInterface;

class TestDocument implements PricedParentInterface, DiscountedInterface
{
    use PricedParentTrait;
    use HasPriceDiscountTrait;
    use HasPriceFeeTrait;

    /** @var TestLine[] */
    public array $lines = [];

    public function addLine(TestLine $line): TestLine
    {
        $this->lines[] = $line;
        $line->setDocument($this);

        return $line;
    }

    public function getPricedChildren(): iterable
    {
        return $this->lines;
    }
}
