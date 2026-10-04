<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Helper\RateHelper;

class RateHelperTest extends TestCase
{
    public function testRateOfRoundsHalfAwayFromZero(): void
    {
        $this->assertSame(2000, RateHelper::rateOf(10000, 2000));
        // 125 × 20 % = 25.
        $this->assertSame(25, RateHelper::rateOf(125, 2000));
        // 5 × 5.5 % = 0.275 → 0; 10 × 5.5 % = 0.55 → 1.
        $this->assertSame(0, RateHelper::rateOf(5, 550));
        $this->assertSame(1, RateHelper::rateOf(10, 550));
        $this->assertSame(-1, RateHelper::rateOf(-10, 550));
    }

    public function testExtractBase(): void
    {
        $this->assertSame(10000, RateHelper::extractBase(12000, 2000));
        $this->assertSame(31818, RateHelper::extractBase(35000, 1000));
    }

    public function testAllocateKeepsTheSum(): void
    {
        $parts = RateHelper::allocate(100, [2000 => 1, 1000 => 1, 550 => 1]);
        $this->assertSame(100, array_sum($parts));
        $this->assertSame([2000, 1000, 550], array_keys($parts));

        $parts = RateHelper::allocate(-7, [1 => 3, 2 => 3]);
        $this->assertSame(-7, array_sum($parts));

        $this->assertSame([1 => 0, 2 => 0], RateHelper::allocate(10, [1 => 0, 2 => 0]));
    }
}
