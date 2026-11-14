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

    /**
     * What a reader types in a field, with or without a thousands separator,
     * read without going through a float.
     */
    public function testFromDecimalAsTyped(): void
    {
        $this->assertSame(123450, MoneyHelper::fromDecimal('1.234,50', 'EUR'));
        $this->assertSame(123450, MoneyHelper::fromDecimal('1 234,50', 'EUR'));
        $this->assertSame(123450, MoneyHelper::fromDecimal("1\u{a0}234,50", 'EUR'));
        $this->assertSame(123450, MoneyHelper::fromDecimal("1\u{202f}234,50", 'EUR'));
        $this->assertSame(123450, MoneyHelper::fromDecimal('1234.5', 'EUR'));
        $this->assertSame(-1230, MoneyHelper::fromDecimal('-12,3', 'EUR'));
        $this->assertSame(-50, MoneyHelper::fromDecimal('-0,50', 'EUR'));
        $this->assertSame(123456789, MoneyHelper::fromDecimal('1.234.567,89', 'EUR'));
        $this->assertSame(123456700, MoneyHelper::fromDecimal('1,234,567', 'EUR'));
        $this->assertSame(1250, MoneyHelper::fromDecimal('12,5 %', 'EUR'));
        $this->assertSame(1999, MoneyHelper::fromDecimal(' 19,99 € ', 'EUR'));
        // Half away from zero, on both sides.
        $this->assertSame(-1000, MoneyHelper::fromDecimal('-9,995', 'EUR'));
        $this->assertSame(999, MoneyHelper::fromDecimal('9,994', 'EUR'));
        // The currency says how many decimals: none for JPY, three for KWD.
        $this->assertSame(1235, MoneyHelper::fromDecimal('1.234,5', 'JPY'));
        $this->assertSame(1234500, MoneyHelper::fromDecimal('1 234,5', 'KWD'));
        // A float typed 0.1 + 0.2 is read for its two decimals.
        $this->assertSame(30, MoneyHelper::fromDecimal(0.1 + 0.2, 'EUR'));
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
