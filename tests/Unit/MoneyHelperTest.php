<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Helper\MoneyHelper;

class MoneyHelperTest extends TestCase
{
    public function testFromDecimal(): void
    {
        $this->assertSame(1999, MoneyHelper::fromDecimal('19.99', 'EUR'));
        $this->assertSame(500, MoneyHelper::fromDecimal('500', 'JPY'));
        $this->assertSame(123456, MoneyHelper::fromDecimal('1 234,56', 'EUR'));
        $this->assertSame(123456, MoneyHelper::fromDecimal('1.234,56', 'EUR'));
        $this->assertSame(123456, MoneyHelper::fromDecimal('1,234.56', 'EUR'));
        $this->assertSame(-1250, MoneyHelper::fromDecimal('-12,5', 'EUR'));
        // Rounds, never truncates: network did (int) (19.99 * 100) = 1998.
        $this->assertSame(1999, MoneyHelper::fromDecimal(19.99, 'EUR'));
        $this->assertSame(1000, MoneyHelper::fromDecimal('9.995', 'EUR'));
        $this->assertSame(0, MoneyHelper::fromDecimal('', 'EUR'));
    }

    public function testToDecimal(): void
    {
        $this->assertSame('19.99', MoneyHelper::toDecimal(1999, 'EUR'));
        $this->assertSame('0.05', MoneyHelper::toDecimal(5, 'EUR'));
        $this->assertSame('-0.05', MoneyHelper::toDecimal(-5, 'EUR'));
        $this->assertSame('500', MoneyHelper::toDecimal(500, 'JPY'));
    }

    public function testDecimals(): void
    {
        $this->assertSame(2, MoneyHelper::getDecimals('EUR'));
        $this->assertSame(0, MoneyHelper::getDecimals('JPY'));
        $this->assertSame(8, MoneyHelper::getDecimals('BTC'));
    }
}
