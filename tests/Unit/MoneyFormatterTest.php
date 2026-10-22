<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Service\MoneyFormatter;

class MoneyFormatterTest extends TestCase
{
    private function normalizeSpaces(string $value): string
    {
        return preg_replace('/[\x{00A0}\x{202F}]/u', ' ', $value);
    }

    public function testFormatFr(): void
    {
        $formatter = new MoneyFormatter('fr');

        $this->assertSame('1 234,56 €', $this->normalizeSpaces($formatter->format(123456, 'EUR')));
        $this->assertSame('1 234,56', $this->normalizeSpaces($formatter->formatNumber(123456, 'EUR')));
        $this->assertSame('20 %', $this->normalizeSpaces($formatter->formatRate(2000)));
        $this->assertSame('5,5 %', $this->normalizeSpaces($formatter->formatRate(550)));
    }

    public function testFormatEn(): void
    {
        $formatter = new MoneyFormatter('en');

        $this->assertSame('€1,234.56', $formatter->format(123456, 'EUR'));
        $this->assertSame('¥500', $formatter->format(500, 'JPY'));
        $this->assertSame('-$0.05', $formatter->format(-5, 'USD'));
    }
}
