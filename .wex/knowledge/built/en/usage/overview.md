A Symfony bundle for applications that store and display monetary amounts: it ships a Doctrine `Currency` entity carrying a code, a symbol, a name and a `decimals` count, typed as either `Currency::TYPE_FIAT` or `Currency::TYPE_CRYPTO`. `CurrencyService::seed()` fills that table from `CurrencyData`, which reads every fiat code from `Symfony\Component\Intl\Currencies` and adds a hand-maintained list of ten crypto-currencies (BTC, ETH, USDT, USDC, BNB, XRP, SOL, ADA, DOGE, TON). Around the entity come the pieces an application would otherwise rewrite — `HasCurrencyCodeTrait` and `HasCurrencySymbolTrait` to attach a currency to your own priced entities, a `CurrencyForm`, and the `api/currency/list` and `api/currency/import` endpoints.

## Prices

Amounts are `int` minor units, rates `int` basis points (2000 = 20 %). A priced entity uses the traits matching its role and implements the interfaces the calculator reads:

```php
class Product implements PricedInterface, VatRatedInterface
{
    use PricedSingleTrait;   // priceRaw, priceTotal, priceOverridden
    use HasPriceVatTrait;    // priceVat
}

class Order implements PricedParentInterface, DiscountedInterface
{
    use PricedParentTrait;       // sums its children
    use HasPriceDiscountTrait;   // money or percent, spread over the VAT bases
}

class OrderLine implements PricedChildInterface, QuantifiedInterface, VatRatedInterface
{
    use PricedChildTrait;    // refreshes its parent
    use HasQuantityTrait;    // scaled by getQuantityScale(): 100 lets 150 mean 1.5
    use HasPriceVatTrait;
}
```

`calcPriceBreakdown()` returns every step — subtotal, discount, one `VatLine` per rate, total, final — computed by `PriceCalculatorHelper`, the only place the rules live. The stored `priceTotal` is refreshed by every setter and never holds the override; `calcPriceFinal()` returns the override when one is set.

A line meant to be copied onto another document — a cart item becoming an invoice item — implements `PricedLineInterface`: a title, goods or services, and the revenue account it is booked on, on top of its price, quantity and rate. The package copying it needs nothing else from the line's own class.

## VAT rates by buyer

`VatRateResolverInterface::resolveVatRate($rate, $goods, $buyerCountryCode, $buyerVatNumber)` gives the rate a line is charged at once the buyer is known. The default `DefaultVatRateResolver` keeps the seller's own rate for everyone. `symfony-accounting` takes its place with the tax rules of the seller's country: no VAT for a business buyer from another EU country whose number VIES confirms (reverse charge), the buyer's country's rate for a consumer abroad once the One-Stop Shop applies.

`MoneyHelper::fromDecimal('1 234,56', 'EUR')` rounds rather than truncates and reads decimal commas; `RateHelper` does integer percentage maths and splits an amount across weights without losing a cent. `MoneyFormatter` and the Twig filters `price`, `price_number` and `rate` format for the locale.


## Exchange rates

`ExchangeRate` holds how much one unit of a currency is worth in another on a day, as a decimal string read to six decimals. `convert()` turns minor units into the target's minor units, rounding without floats, whatever the currencies' decimals. `ExchangeRateService::getRate($from, $to, $date)` asks the installed `ExchangeRateProviderInterface`, which `symfony-remote-ecb` implements with the European Central Bank's reference rates. Without a provider, only a currency against itself has a rate, and other rates are typed in.
