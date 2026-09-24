# Extract the priced-entity engine from network

Opened: 2026-09-24
Updated: 2026-09-24
Author: agent:archeology

## Read this first — status of this todo

> **This is a proposal for discussion, not an order to code.** It was written by the 2026-09 network archaeology pass. Read it, then discuss it with the owner: every design choice and recommendation below is to be challenged and validated **before** any code is written. Do not start implementing on your own.
>
> - Context: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/index.md.j2` (entry point, order between packages), then `sources.md.j2` (where the legacy code lives: archive repo, branch checkouts, GitLab issues) and the domain page linked below.
> - Pending owner decisions affecting this work are listed in `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/recap.md.j2`, section "Décisions qui t'attendent". Where this todo assumes an answer, treat it as an open question.
> - Safety: `NETWORK/local/network` runs on **production data** (real bookkeeping, real invoices in `var/`, a prod dump in `.wex/mysql/dumps/`) — read its code only, never run anything against it. Anonymize any fixture taken from network (bank exports, FEC, mails contain real names/accounts). Never copy secrets found in its history (Stripe keys, tokens, passwords, private keys).

## Goal

Give `symfony-money` the integer-cents pricing engine that network uses for products, carts, cart items, invoices and invoice items: raw / total / overridden prices, VAT per item, quantity, parent/child aggregation, fee and discount with money-or-percent units. `symfony-cart` and `symfony-accounting` will build on it. Today the package only has `Currency`. The 0.x `PricedEntityServiceTrait` and `PricedFormProcessorTrait` were dropped, and prod network still uses them from `vendor/wexample/symfony-money` 0.x.

Knowledge page: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/cart-payment.md.j2`. Read the sections "Money conventions" and "Priced-entity engine", plus the Pitfalls. Also read `accounting.md.j2` in the same folder, because invoices use the same traits.

Issues:
- `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/gitlab/issues/135.md`: VAT per item.
- `056.md`: TTC/HT, and a separate external fee.
- `222.md`: membership booked HT.

## Prerequisites

- `symfony-helpers`, which already has `Helper/PriceHelper.php` with percentage helpers and `ZERO_DECIMAL_CURRENCIES` / `buildPriceFromFloat()`.
- The `Currency` entity already in this package.
- Coordinate with whoever extracts invoices into `symfony-accounting`. The prod Invoice (`/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/src/Entity/Invoice.php`) still uses the older flat model with invoice-level VAT, while fos uses the tree model below. The traits must support both until the accounting package migrates.

## Decisions already implied

- Amounts are `int` in minor units (cents). VAT is an `int` in basis points (`2000` = 20 %). There are no floats in storage.
- Prices are snapshotted: a child copies the product's prices when created.
- A unit is either money or percent. Use a PHP enum (`PriceUnit::Money|Percent`), not `AccountingService::UNIT_*` strings.
- Currency uses this package's `HasCurrencyCodeTrait`, not the `PaymentEntityService::CURRENCY_*` constants.

## Steps

1. Read the sources, all under `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/`:
   - `Wex/BaseBundle/Entity/Traits/PricedEntityTrait.php`
   - `PricedTypeSingle.php`, `PricedTypeParentTrait.php`, `PricedTypeChildTrait.php`, `PricedTypeParentWithVatChildrenTrait.php`
   - `PricedWithVatTrait.php`, `WithVatEntityTrait.php`
   - `PricedWithFeeEntityTrait.php`, `PricedWithDiscountEntityTrait.php`, `PricedWithCurrencyEntityTrait.php`
   - `WithAmountTrait.php`, `WithCurrencyTrait.php`
   - `Entity/Traits/HasPricedQuantityTrait.php`, `Entity/Traits/HasQuantityTrait.php`
   - `Wex/BaseBundle/Service/Traits/PricedEntityServiceTrait.php`, `Wex/BaseBundle/Helper/PriceHelper.php`

   Compare with the flat prod versions in `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/src/Wex/BaseBundle/Entity/Traits/` and `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/vendor/wexample/symfony-money/src/`.
2. Run `wex ai::design/rules --formatter php-code` in this package and follow its layout. Mirror the existing `src/Entity/Traits/` convention of this package for entity traits.
3. Port the traits with modern names and no `App\` references:
   - `PricedEntityTrait`
   - `PricedSingleTrait`
   - `PricedParentTrait`, `PricedParentWithVatChildrenTrait`, `PricedChildTrait`
   - `PricedWithVatTrait`
   - `HasPricedQuantityTrait` (needs a quantity: add `HasQuantityTrait` here or in `symfony-helpers`)
   - `PricedWithFeeTrait`, `PricedWithDiscountTrait`

   Keep this behaviour:
   - Every setter calls `updatePriceTotal()`.
   - A child propagates the update to its parent.
   - A parent's `calcPriceTotal` is the sum of its children's `calcPriceFinal`.

   Replace display methods that output HTML (`displayPrice*` with `&nbsp;`) with a formatting service or Twig filter. Entities must not format money.
4. Add `Interface/PricedEntityInterface`, `PricedParentInterface` and `PricedChildInterface`, so services type against interfaces instead of `$entity->setPriceTotal()` duck typing.
5. Port `PricedEntityServiceTrait`. **Fix it**: stop writing the override into `priceTotal`. Keep the computed total and the override apart. `calcPriceFinal()` returns the override when set.
6. Add a `MoneyHelper` (or extend `PriceHelper` in `symfony-helpers`) with `fromDecimal(string|float $amount, string $currencyCode): int`. It must round, not cast: network truncates with `(int) ($amount * 100)`. Use `Currency.decimals` or `ZERO_DECIMAL_CURRENCIES`.
7. Optional: port `vendor/wexample/symfony-money/src/Service/FormProcessor/Traits/PricedFormProcessorTrait.php` if a form needs cents↔decimal conversion. Check `symfony-forms` for an existing money field first.

## Do not

- Do not import anything from `App\`, `AccountingService` or `PaymentEntityService`.
- Do not add Doctrine columns with float types.
- Do not port `displayPriceByUnit` / `displayPrice` HTML output into entities.
- Do not port PDF code.

## Acceptance (tests inside the package, `tests/Unit/...`)

- Port `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/tests/Unit/Accounting/Pricing/CartPricingTest.php` and `ProductPricingTest.php` as pure unit tests, with fixture classes implementing the traits (no DB):
  - raw 125 with VAT 2000 and quantity 10 gives total 1500.
  - An override of 11111 wins.
  - Product 25000 + 20 % = 30000.
  - A quantity of 2 in the parent gives 60000.
- Propagation test: changing a child's quantity updates the parent's total.
- Test for a fee in percent vs money, and a discount in percent vs money.
- Test that `fromDecimal('19.99', 'EUR') === 1999` and `fromDecimal('500', 'JPY') === 500`.
