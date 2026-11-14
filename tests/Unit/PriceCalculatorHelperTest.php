<?php

declare(strict_types=1);

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Enum\PriceUnit;
use Wexample\SymfonyMoney\Helper\PriceCalculatorHelper;
use Wexample\SymfonyMoney\Tests\Unit\Fixtures\TestDocument;
use Wexample\SymfonyMoney\Tests\Unit\Fixtures\TestLine;

/**
 * A parent's price: its children's bases summed by rate, its discount spread
 * over those bases, the VAT of each rate computed once on its base.
 */
class PriceCalculatorHelperTest extends TestCase
{
    public function testAnEmptyParentCostsNothing(): void
    {
        $breakdown = PriceCalculatorHelper::parent(new TestDocument());

        $this->assertSame(0, $breakdown->subTotal);
        $this->assertSame([], $breakdown->vatLines);
        $this->assertSame(0, $breakdown->getFinal());
    }

    public function testTheVatOfARateIsRoundedOnceOnItsBase(): void
    {
        $document = new TestDocument();
        foreach ([1, 2, 3] as $ignored) {
            $document->addLine(new TestLine())->setPriceRaw(333)->setPriceVat(2000);
        }

        // Each line alone: 66.6 → 67, 2.01 in all; on the base of 9.99: 2.00.
        $this->assertSame(67, $document->lines[0]->calcPriceVat());
        $breakdown = PriceCalculatorHelper::parent($document);
        $this->assertSame(999, $breakdown->getVatLine(2000)->base);
        $this->assertSame(200, $breakdown->getVatLine(2000)->vat);
        $this->assertSame(1199, $breakdown->getFinal());
    }

    public function testRatesComeInOrderEachWithItsBase(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);
        $document->addLine(new TestLine())->setPriceRaw(4000)->setPriceVat(0);
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(550);

        $breakdown = PriceCalculatorHelper::parent($document);

        $this->assertSame([0, 550, 2000], array_keys($breakdown->vatLines));
        $this->assertSame(24000, $breakdown->subTotal);
        $this->assertSame(0 + 550 + 2000, $breakdown->getVat());
    }

    public function testAnOverriddenChildCountsForTheBaseItsPriceImplies(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);
        // Sold 120.00 VAT included, whatever its raw price says: 100.00 of base.
        $document->addLine(new TestLine())->setPriceRaw(500)->setPriceVat(2000)->setPriceOverridden(12000);

        $breakdown = PriceCalculatorHelper::parent($document);

        $this->assertSame(20000, $breakdown->getVatLine(2000)->base);
        $this->assertSame(4000, $breakdown->getVatLine(2000)->vat);
        $this->assertSame(24000, $breakdown->getFinal());
        $this->assertFalse($breakdown->isOverridden());
    }

    public function testAPercentDiscountLowersEveryBase(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(550);
        $document->setPriceDiscount(1000, PriceUnit::Percent);

        $breakdown = PriceCalculatorHelper::parent($document);

        $this->assertSame(2000, $breakdown->discount);
        $this->assertSame(9000, $breakdown->getVatLine(2000)->base);
        $this->assertSame(9000, $breakdown->getVatLine(550)->base);
        $this->assertSame(1800 + 495, $breakdown->getVat());
        $this->assertSame(18000 + 2295, $breakdown->getFinal());
    }

    public function testAnAmountDiscountIsSpreadToTheCent(): void
    {
        $document = new TestDocument();
        foreach ([2000, 550, 0] as $rate) {
            $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat($rate);
        }
        $document->setPriceDiscount(100, PriceUnit::Money);

        $breakdown = PriceCalculatorHelper::parent($document);

        // 1.00 over three equal bases: 0.33, 0.33, 0.34 — never a cent lost.
        $this->assertSame(100, $breakdown->discount);
        $bases = array_map(static fn ($line) => $line->base, $breakdown->vatLines);
        $this->assertSame(30000 - 100, array_sum($bases));
        $this->assertEqualsCanonicalizing([9966, 9967, 9967], array_values($bases));
    }

    public function testTheParentOverrideReplacesOnlyTheFinalPrice(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);
        $document->setPriceOverridden(11000);

        $breakdown = PriceCalculatorHelper::parent($document);

        $this->assertSame(12000, $breakdown->getTotal());
        $this->assertSame(11000, $breakdown->getFinal());
        $this->assertTrue($breakdown->isOverridden());
    }
}
