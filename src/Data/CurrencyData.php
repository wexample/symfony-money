<?php

namespace Wexample\SymfonyMoney\Data;

use Symfony\Component\Intl\Currencies;
use Wexample\SymfonyMoney\Constant\CryptoCurrencyConstant;
use Wexample\SymfonyMoney\Entity\Currency;

class CurrencyData
{
    private const CRYPTO = [
        CryptoCurrencyConstant::BTC => ['name' => 'Bitcoin',  'symbol' => CryptoCurrencyConstant::SYMBOL_BTC,  'decimals' => 8],
        CryptoCurrencyConstant::ETH => ['name' => 'Ethereum', 'symbol' => CryptoCurrencyConstant::SYMBOL_ETH,  'decimals' => 18],
        CryptoCurrencyConstant::USDT => ['name' => 'Tether',   'symbol' => CryptoCurrencyConstant::SYMBOL_USDT, 'decimals' => 6],
        CryptoCurrencyConstant::USDC => ['name' => 'USD Coin', 'symbol' => CryptoCurrencyConstant::SYMBOL_USDC, 'decimals' => 6],
        CryptoCurrencyConstant::BNB => ['name' => 'BNB',      'symbol' => CryptoCurrencyConstant::SYMBOL_BNB,  'decimals' => 8],
        CryptoCurrencyConstant::XRP => ['name' => 'XRP',      'symbol' => CryptoCurrencyConstant::SYMBOL_XRP,  'decimals' => 6],
        CryptoCurrencyConstant::SOL => ['name' => 'Solana',   'symbol' => CryptoCurrencyConstant::SYMBOL_SOL,  'decimals' => 9],
        CryptoCurrencyConstant::ADA => ['name' => 'Cardano',  'symbol' => CryptoCurrencyConstant::SYMBOL_ADA,  'decimals' => 6],
        CryptoCurrencyConstant::DOGE => ['name' => 'Dogecoin', 'symbol' => CryptoCurrencyConstant::SYMBOL_DOGE, 'decimals' => 8],
        CryptoCurrencyConstant::TON => ['name' => 'Toncoin',  'symbol' => CryptoCurrencyConstant::SYMBOL_TON,  'decimals' => 9],
    ];

    public static function getFiatCurrencies(): array
    {
        $result = [];

        foreach (Currencies::getCurrencyCodes() as $code) {
            $result[$code] = [
                'code' => $code,
                'name' => Currencies::getName($code, 'en'),
                'symbol' => Currencies::getSymbol($code, 'en'),
                'decimals' => Currencies::getFractionDigits($code),
                'type' => Currency::TYPE_FIAT,
            ];
        }

        return $result;
    }

    public static function getCryptoCurrencies(): array
    {
        $result = [];

        foreach (self::CRYPTO as $code => $data) {
            $result[$code] = [
                'code' => $code,
                'name' => $data['name'],
                'symbol' => $data['symbol'],
                'decimals' => $data['decimals'],
                'type' => Currency::TYPE_CRYPTO,
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
