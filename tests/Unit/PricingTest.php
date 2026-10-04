<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Enum\PriceUnit;
use Wexample\SymfonyMoney\Tests\Unit\Fixtures\TestDocument;
use Wexample\SymfonyMoney\Tests\Unit\Fixtures\TestLine;
use Wexample\SymfonyMoney\Tests\Unit\Fixtures\TestProduct;

class PricingTest extends TestCase
{
    public function testProduct(): void
    {
        $product = (new TestProduct())->setPriceRaw(25000)->setPriceVat(2000);

        $this->assertSame(25000, $product->calcPriceSubTotal());
        $this->assertSame(5000, $product->calcPriceVat());
        $this->assertSame(30000, $product->calcPriceFinal());
        $this->assertSame(30000, $product->getPriceTotal());
    }

    public function testLineWithQuantityAndVat(): void
    {
        $document = new TestDocument();
        $line = $document->addLine(new TestLine());
        $line->setPriceRaw(125)->setPriceVat(2000);

        $this->assertSame(150, $line->calcPriceFinal());
        $this->assertSame(150, $document->calcPriceFinal());

        $line->setQuantity(10);

        $this->assertSame(1250, $line->calcPriceSubTotal());
        $this->assertSame(1500, $line->calcPriceTotal());
        // The parent's stored total followed the child.
        $this->assertSame(1500, $document->getPriceTotal());
    }

    public function testOverrideIsKeptApartFromTotal(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(125)->setPriceVat(2000)->setQuantity(10);

        $document->setPriceOverridden(11111);

        $this->assertSame(1500, $document->calcPriceTotal());
        $this->assertSame(1500, $document->getPriceTotal());
        $this->assertSame(11111, $document->calcPriceFinal());

        $document->setPriceOverridden(null);
        $this->assertSame(1500, $document->calcPriceFinal());
    }

    public function testScaledQuantity(): void
    {
        $document = new TestDocument();
        // 26 days at 125.00/day, quantity scale 100.
        $line = $document->addLine(new TestLine(100));
        $line->setPriceRaw(12500)->setPriceVat(2000)->setQuantity(2600);

        $this->assertSame(325000, $document->calcPriceSubTotal());
        $this->assertSame(390000, $document->calcPriceFinal());

        $line->setQuantity(150);
        $this->assertSame(18750, $line->calcPriceSubTotal());
    }

    public function testDiscountLowersTheTaxableBase(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine(100))->setPriceRaw(12500)->setPriceVat(2000)->setQuantity(2600);

        $document->setPriceDiscount(1000, PriceUnit::Money);
        $this->assertSame(324000, $document->calcPriceNet());
        $this->assertSame(64800, $document->calcPriceVat());
        $this->assertSame(388800, $document->calcPriceFinal());

        // 10 % in basis points.
        $document->setPriceDiscount(1000, PriceUnit::Percent);
        $this->assertSame(292500, $document->calcPriceNet());
        $this->assertSame(351000, $document->calcPriceFinal());

        $document->setPriceDiscount(null);
        $this->assertSame(390000, $document->calcPriceFinal());
    }

    public function testMultiRateTotalsAndSpreadDiscount(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(550);
        $document->addLine(new TestLine())->setPriceRaw(5000)->setPriceVat(2000);

        $lines = $document->calcVatLines();
        $this->assertSame([550, 2000], array_keys($lines));
        $this->assertSame(15000, $lines[2000]->base);
        $this->assertSame(3000, $lines[2000]->vat);
        $this->assertSame(10000, $lines[550]->base);
        $this->assertSame(550, $lines[550]->vat);
        $this->assertSame(28550, $document->calcPriceFinal());

        // 50.00 off, spread 30.00 / 20.00 in proportion to the bases.
        $document->setPriceDiscount(5000, PriceUnit::Money);
        $lines = $document->calcVatLines();
        $this->assertSame(12000, $lines[2000]->base);
        $this->assertSame(8000, $lines[550]->base);
        $this->assertSame(2400 + 440, $document->calcPriceVat());
        $this->assertSame(20000 + 2840, $document->calcPriceFinal());
    }

    public function testLineDiscount(): void
    {
        $document = new TestDocument();
        $line = $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);
        $line->setPriceDiscount(2500, PriceUnit::Percent);

        $this->assertSame(7500, $line->calcPriceNet());
        $this->assertSame(9000, $line->calcPriceFinal());
        $this->assertSame(9000, $document->calcPriceFinal());
    }

    public function testOverriddenChildCountsInParent(): void
    {
        $document = new TestDocument();
        $line = $document->addLine(new TestLine())->setPriceRaw(500)->setPriceVat(1000);
        $line->setPriceOverridden(35000);

        $this->assertSame(35000, $line->calcPriceFinal());
        $this->assertSame(35000, $document->calcPriceFinal());
    }

    public function testFee(): void
    {
        $document = new TestDocument();
        $document->addLine(new TestLine())->setPriceRaw(10000)->setPriceVat(2000);

        $document->setPriceFee(300);
        $this->assertSame(300, $document->calcPriceFee());

        $document->setPriceFee(450, PriceUnit::Money);
        $this->assertSame(450, $document->calcPriceFee());
        // A fee never changes the price.
        $this->assertSame(12000, $document->calcPriceFinal());
    }
}
