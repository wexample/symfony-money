<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Class\ExchangeRate;
use Wexample\SymfonyMoney\Interface\ExchangeRateProviderInterface;
use Wexample\SymfonyMoney\Service\ExchangeRateService;

class ExchangeRateTest extends TestCase
{
    public function testAnAmountIsConvertedInTheTargetMinorUnit(): void
    {
        $date = new DateTimeImmutable('2026-03-02');
        $usdToEur = new ExchangeRate('USD', 'EUR', '0.92213', $date);

        $this->assertSame(922130, $usdToEur->scaled());
        $this->assertSame(92213, $usdToEur->convert(100000));
        // Half away from zero, both ways.
        $this->assertSame(1, $usdToEur->convert(1));
        $this->assertSame(-92213, $usdToEur->convert(-100000));
        // Currencies without minor units.
        $this->assertSame(16050, (new ExchangeRate('EUR', 'JPY', '160.5', $date))->convert(10000));
        $this->assertSame(6230, (new ExchangeRate('JPY', 'EUR', '0.00623', $date))->convert(10000));
        $this->assertSame('1.084446', $usdToEur->invert()->rate);
        $this->assertSame(10000, ExchangeRate::identity('EUR', $date)->convert(10000));
    }

    public function testASeventhDecimalRounds(): void
    {
        $this->assertSame(1084457, (new ExchangeRate('EUR', 'USD', '1.0844565', new DateTimeImmutable()))->scaled());
    }

    public function testARateIsPositive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExchangeRate('EUR', 'USD', '0', new DateTimeImmutable());
    }

    public function testWithoutProviderOnlyACurrencyAgainstItselfHasARate(): void
    {
        $date = new DateTimeImmutable('2026-03-02');
        $service = new ExchangeRateService();

        $this->assertSame('1', $service->getRate('eur', 'EUR', $date)->rate);
        $this->assertNull($service->getRate('USD', 'EUR', $date));

        $provider = new class implements ExchangeRateProviderInterface {
            public function getRate(string $from, string $to, DateTimeImmutable $date): ?ExchangeRate
            {
                return new ExchangeRate($from, $to, '0.92', $date, 'test');
            }
        };

        $this->assertSame('test', (new ExchangeRateService($provider))->getRate('usd', 'eur', $date)->source);
    }
}
