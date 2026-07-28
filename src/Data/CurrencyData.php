<?php

namespace Wexample\SymfonyMoney\Data;

use Symfony\Component\Intl\Currencies;
use Wexample\SymfonyMoney\Entity\AbstractCurrency;
use Wexample\SymfonyMoney\Helper\CurrencyHelper;

class CurrencyData
{
    private const CRYPTO = [
        CurrencyHelper::BTC  => ['name' => 'Bitcoin',   'symbol' => CurrencyHelper::SYMBOL_BTC,  'decimals' => 8],
        CurrencyHelper::ETH  => ['name' => 'Ethereum',  'symbol' => CurrencyHelper::SYMBOL_ETH,  'decimals' => 18],
        CurrencyHelper::USDT => ['name' => 'Tether',    'symbol' => CurrencyHelper::SYMBOL_USDT, 'decimals' => 6],
        CurrencyHelper::USDC => ['name' => 'USD Coin',  'symbol' => CurrencyHelper::SYMBOL_USDC, 'decimals' => 6],
        CurrencyHelper::BNB  => ['name' => 'BNB',       'symbol' => CurrencyHelper::SYMBOL_BNB,  'decimals' => 8],
        CurrencyHelper::XRP  => ['name' => 'XRP',       'symbol' => CurrencyHelper::SYMBOL_XRP,  'decimals' => 6],
        CurrencyHelper::SOL  => ['name' => 'Solana',    'symbol' => CurrencyHelper::SYMBOL_SOL,  'decimals' => 9],
        CurrencyHelper::ADA  => ['name' => 'Cardano',   'symbol' => CurrencyHelper::SYMBOL_ADA,  'decimals' => 6],
        CurrencyHelper::DOGE => ['name' => 'Dogecoin',  'symbol' => CurrencyHelper::SYMBOL_DOGE, 'decimals' => 8],
        CurrencyHelper::TON  => ['name' => 'Toncoin',   'symbol' => CurrencyHelper::SYMBOL_TON,  'decimals' => 9],
    ];

    public static function getFiatCurrencies(): array
    {
        $result = [];

        foreach (Currencies::getCurrencyCodes() as $code) {
            $result[$code] = [
                'code'     => $code,
                'name'     => Currencies::getName($code, 'en'),
                'symbol'   => Currencies::getSymbol($code, 'en'),
                'decimals' => Currencies::getFractionDigits($code),
                'type'     => AbstractCurrency::TYPE_FIAT,
            ];
        }

        return $result;
    }

    public static function getCryptoCurrencies(): array
    {
        $result = [];

        foreach (self::CRYPTO as $code => $data) {
            $result[$code] = [
                'code'     => $code,
                'name'     => $data['name'],
                'symbol'   => $data['symbol'],
                'decimals' => $data['decimals'],
                'type'     => AbstractCurrency::TYPE_CRYPTO,
            ];
        }

        return $result;
    }

    public static function getAll(): array
    {
        return array_merge(
            self::getFiatCurrencies(),
            self::getCryptoCurrencies()
        );
    }
}
