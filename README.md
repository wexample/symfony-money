# symfony_money

Version: 3.0.1

A Symfony bundle for applications that store and display monetary amounts: it ships a Doctrine `Currency` entity carrying a code, a symbol, a name and a `decimals` count, typed as either `Currency::TYPE_FIAT` or `Currency::TYPE_CRYPTO`. `CurrencyService::seed()` fills that table from `CurrencyData`, which reads every fiat code from `Symfony\Component\Intl\Currencies` and adds a hand-maintained list of ten crypto-currencies (BTC, ETH, USDT, USDC, BNB, XRP, SOL, ADA, DOGE, TON). Around the entity come the pieces an application would otherwise rewrite — `HasCurrencyCodeTrait` and `HasCurrencySymbolTrait` to attach a currency to your own priced entities, a `CurrencyForm`, and the `api/currency/list` and `api/currency/import` endpoints.

## Table of Contents

- [Architecture](#architecture)
- [Integration in the Suite](#integration-in-the-suite)
- [Dependencies](#dependencies)
- [Versioning & Compatibility Policy](#versioning--compatibility-policy)
- [License](#license)
- [About us](#about-us)
- [Migration Notes](#migration-notes)

## Architecture

The package is a single Symfony bundle around a single Doctrine entity, `Currency`. PHP lives under `src/` (PSR-4 `Wexample\SymfonyMoney\`), and the browser-side half — TypeScript entity, form template, translations — lives under `assets/`, which the bundle exposes to the loader. There is no `tests/` directory and no build step: Composer autoloading is the whole packaging.

### Bundle entry point and wiring

src/WexampleSymfonyMoneyBundle.php extends `AbstractBundle` and declares what the two suite bundles that scan it need:

```php
class WexampleSymfonyMoneyBundle extends AbstractBundle implements PseudocodeBundleInterface, LoaderBundleInterface
```

`getPseudocodeSourcePaths()` returns `[__DIR__ . '/']`, so `symfony-pseudocode` walks the whole of `src/` — that is what produces `assets/data/entity/currency.json` from the `#[PseudocodeExport(inherited: true)]` attribute on the entity. `getLoaderFrontPaths()` maps `BundleHelper::getBundleCssAlias(static::class)` to `__DIR__ . '/../assets/'`, which is how the Twig template and the `.en.yml` translation files become reachable as `@WexampleSymfonyMoneyBundle`.

src/DependencyInjection/WexampleSymfonyMoneyExtension.php does nothing but `$this->loadConfig(__DIR__, $container)`, which picks up both files in `src/Resources/config/`. src/Resources/config/services.yaml autowires three directories as one group:

```yaml
    Wexample\SymfonyMoney\:
        resource: '../../{Form,Repository,Service}'
```

Controllers get `controller.service_arguments`, normalizers get `serializer.normalizer`, and an `_instanceof` rule tags every `AbstractFormProcessor` subclass with `wexample.symfony_forms.form_processor` — the tag `symfony-forms` uses to find a processor by its form class. src/Resources/config/routes.yaml imports `../../Api/Controller/` with `type: attribute`; routes are declared on the methods, not in YAML.

### The parts

`Entity/` — src/Entity/Currency.php is a thin composition of traits. Two are local, `HasCurrencyCodeTrait` (a `unique: true` column of length 10) and `HasCurrencySymbolTrait`; the rest (`HasDecimalsTrait`, `HasNameTrait`, `HasSecureIdTrait`, `HasTypeTrait`) come from `symfony-helpers`. The class itself only fixes two things: the secure-id prefix `'cry'`, and the two allowed types `Currency::TYPE_FIAT` and `Currency::TYPE_CRYPTO` returned by `getAllowedTypes()`.

`Entity/Traits/Manipulator/` — src/Entity/Traits/Manipulator/CurrencyEntityManipulatorTrait.php is a two-line trait declaring `getEntityClassName(): string { return Currency::class; }`. Both the repository and the normalizer use it; it is how the helpers' generic entity machinery learns which class it is operating on.

`Repository/` — src/Repository/CurrencyRepository.php adds three methods over `AbstractRepository`: `createNewCurrency()` (builds the object and calls `setGeneratedSecureId()`, without persisting), `findByCurrencyCode()`, `findAllByType()`. Its docblock also declares the inherited magic methods it relies on, notably `saveNewCurrency()` and `findOneByCurrencyCode()`.

`Data/` and `Constant/` — src/Data/CurrencyData.php is the seed source of truth. Fiat currencies are not stored in the package at all; `getFiatCurrencies()` loops over `Currencies::getCurrencyCodes()` from `symfony/intl` and takes name, symbol and `getFractionDigits()` from there. Crypto currencies are a hard-coded private `CRYPTO` array of ten entries whose codes and symbols reference src/Constant/CryptoCurrencyConstant.php. `getAll()` merges the two. src/Constant/FiatCurrencyConstant.php is a flat list of ISO 4217 codes and symbols for callers that need a compile-time constant — the seeding path does not read it.

`Service/` — src/Service/CurrencyService.php holds one method, `seed()`, described below.

`Api/` — a controller, a normalizer and a DTO, each in its own subtree. `PublicCurrencyDto` is the only shape the API exposes: `secureId`, `currencyCode`, `currencySymbol`, `decimals`, `name`, `type`.

`Form/` and `Service/FormProcessor/` — the admin-side triplet: the form definition, the processor that validates and saves, and a data resolver that turns a request into the entity to edit.

`assets/` — `Entity/Currency.ts` and `Repository/CurrencyRepository.ts` mirror the PHP pair for `@wexample/js-api`, `forms/currency_form.html.twig` renders the form, and the two `.en.yml` files carry the labels the form and the entity display.

### Path of a call

**Seeding.** `POST api/currency/import` reaches `CurrencyController::import()`, which delegates entirely to `CurrencyService::seed()`. For each entry of `CurrencyData::getAll()`, the service looks the row up by `findByCurrencyCode($data['code'])`: if it exists it re-sets name, symbol, decimals and type then calls `$this->currencyRepository->save($currency)`; otherwise it calls `saveNewCurrency(...)` with the same five values. The operation is an upsert keyed on the currency code, so re-importing refreshes rather than duplicates.

**Listing.** `GET api/currency/list` carries `#[PageQueryOption]` and `#[LengthQueryOption]`. The controller builds a pagination object from `$currencyRepository->countAll()`, fetches `findPaginated(page:, length:)`, and hands the result to `DefaultCurrencyNormalizer::normalizeCollection()`. The normalizer's own `normalizeEntity()` is a single `return PublicCurrencyDto::fromEntity($entity);`. The response goes back through `self::apiResponsePaginated()`.

**Editing.** `CurrencyFormDataResolver::resolve()` reads the `secureId` route attribute, loads the matching row and throws `NotFoundHttpException` if there is none. `CurrencyForm` binds five fields to `data_class => Currency::class` under the translation domain `WexampleSymfonyMoneyBundle.forms.currency_form`, with the `type` select fed by `Currency::getAllowedTypes()`. On submit, `CurrencyFormProcessor::onValid()` re-checks code uniqueness against the repository and, on collision, calls `addFormErrorFromApiKey($form, 'ERR_CURRENCY_CODE_ALREADY_USED')` — the key resolved by `assets/forms/currency_form.en.yml`. Otherwise it generates the secure id when the entity is new, persists, flushes, and sets the success action and notification.

### Where the bundle stops

The scope is the currency table and its edges. There is no amount or money value object here, no arithmetic, no exchange rate, and no price entity: an application stores its own amounts and attaches a currency to them. The two intended attachment points are `HasCurrencyCodeTrait` and `HasCurrencySymbolTrait`, meant to be used on your entities rather than only on `Currency` — which is why they live in `Entity/Traits/` and not inside the entity file.

## Integration in the Suite

This package is part of the Wexample Suite — a collection of high-quality, modular tools designed to work seamlessly together across multiple languages and environments.

### Related Packages

The suite includes packages for configuration management, file handling, prompts, and more. Each package can be used independently or as part of the integrated suite.

Visit the [Wexample Suite documentation](https://docs.wexample.com) for the complete package ecosystem.

## Dependencies

- wexample/symfony-helpers: >=7.0.0
- wexample/symfony-api: >=4.0.0
- wexample/symfony-forms: >=3.0.0
- wexample/php-pseudocode: >=1.0.0
- wexample/symfony-pseudocode: >=2.0.0
- symfony/intl: >=6.2

## Versioning & Compatibility Policy

Wexample packages follow **Semantic Versioning** (SemVer):

- **MAJOR**: Breaking changes
- **MINOR**: New features, backward compatible
- **PATCH**: Bug fixes, backward compatible

We maintain backward compatibility within major versions and provide clear migration guides for breaking changes.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Free to use in both personal and commercial projects.

## About us

[Wexample](https://wexample.com) stands as a cornerstone of the digital ecosystem — a collective of seasoned engineers, researchers, and creators driven by a relentless pursuit of technological excellence. More than a media platform, it has grown into a vibrant community where innovation meets craftsmanship, and where every line of code reflects a commitment to clarity, durability, and shared intelligence.

This packages suite embodies this spirit. Trusted by professionals and enthusiasts alike, it delivers a consistent, high-quality foundation for modern development — open, elegant, and battle-tested. Its reputation is built on years of collaboration, refinement, and rigorous attention to detail, making it a natural choice for those who demand both robustness and beauty in their tools.

Wexample cultivates a culture of mastery. Each package, each contribution carries the mark of a community that values precision, ethics, and innovation — a community proud to shape the future of digital craftsmanship.

## Migration Notes

When upgrading between major versions, refer to the migration guides in the documentation.

Breaking changes are clearly documented with upgrade paths and examples.
