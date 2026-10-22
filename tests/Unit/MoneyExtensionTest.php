<?php

namespace Wexample\SymfonyMoney\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Wexample\SymfonyMoney\Service\MoneyFormatter;
use Wexample\SymfonyMoney\Twig\MoneyExtension;

class MoneyExtensionTest extends TestCase
{
    private function createExtension(string $locale): MoneyExtension
    {
        $translator = new Translator($locale);
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->setFallbackLocales(['en']);

        foreach (['en', 'fr', 'fr_BE'] as $catalogue) {
            $translator->addResource('yaml', __DIR__.'/../../src/Resources/translations/money.'.$catalogue.'.yaml', $catalogue, 'money');
        }

        return new MoneyExtension(new MoneyFormatter($locale), $translator);
    }

    private function normalizeSpaces(string $value): string
    {
        return preg_replace('/[\x{00A0}\x{202F}]/u', ' ', $value);
    }

    public function testTaxSuffixFollowsTheLocale(): void
    {
        $this->assertSame('1 234,56 € HT', $this->normalizeSpaces($this->createExtension('fr')->priceExclTax(123456, 'EUR')));
        $this->assertSame('1 234,56 € TTC', $this->normalizeSpaces($this->createExtension('fr')->priceInclTax(123456, 'EUR')));
        $this->assertSame('€1,234.56 excl. VAT', $this->createExtension('en')->priceExclTax(123456, 'EUR'));
        $this->assertSame('€1,234.56 incl. VAT', $this->createExtension('en')->priceInclTax(123456, 'EUR'));
    }

    public function testBelgiumHasItsOwnWords(): void
    {
        $this->assertSame('1 234,56 € HTVA', $this->normalizeSpaces($this->createExtension('fr_BE')->priceExclTax(123456, 'EUR')));
        $this->assertSame('1 234,56 € TVAC', $this->normalizeSpaces($this->createExtension('fr_BE')->priceInclTax(123456, 'EUR')));
    }

    public function testAnotherLocaleIsGivenPerCall(): void
    {
        $this->assertSame('$12.00 excl. VAT', $this->createExtension('fr')->priceExclTax(1200, 'USD', 'en'));
    }
}
