# Money: front formatting and Twig price filters from network (complements extract-from-network.md)

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

`extract-from-network.md` (cart/payment archaeology) covers the priced-entity engine. This small companion covers the display side that the base-bundle sweep found: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/already-extracted-check.md.j2`.

## Steps

1. Twig `price` / `price_with_vat_suffix` (cents → "12,34 €" + HT/TTC suffix): `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Twig/PriceExtension.php` (prod: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/src/Wex/BaseBundle/Twig/NumbersExtension.php`) + `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Resources/translations/priced.fr.yml`. Use a locale-aware formatter. Tests fr/en.
2. JS formatting: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Resources/js/services/NumberService.ts` (formatPrice, formatCurrency, parse, compact "1k" used by the charts). #46: the JS output must match the PHP output (comma decimal separator in fr), and currencies other than EUR must work. Add it to the package assets. Unit test.
3. Live price/VAT computation on priced forms: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Resources/js/forms/priced.ts`.
4. #27: percentage maths belong in a number helper, not in PriceHelper.
