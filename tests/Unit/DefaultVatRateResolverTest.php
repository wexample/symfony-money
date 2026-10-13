<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyMoney\Service\DefaultVatRateResolver;

class DefaultVatRateResolverTest extends TestCase
{
    public function testEveryBuyerPaysTheSellersRate(): void
    {
        $resolver = new DefaultVatRateResolver();

        $this->assertSame(2000, $resolver->resolveVatRate(2000, false, null, null));
        $this->assertSame(550, $resolver->resolveVatRate(550, true, 'DE', 'DE123456789'));
    }
}
