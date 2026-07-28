<?php

namespace Wexample\SymfonyMoney\Service;

use Wexample\SymfonyMoney\Data\CurrencyData;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;

class CurrencyService
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
    ) {}

    public function seed(): void
    {
        foreach (CurrencyData::getAll() as $data) {
            $currency = $this->currencyRepository->findByCode($data['code']);

            if ($currency) {
                $currency->setName($data['name'])
                    ->setSymbol($data['symbol'])
                    ->setDecimals($data['decimals']);
                $currency->setType($data['type']);

                $this->currencyRepository->save($currency);
            } else {
                $this->currencyRepository->saveNewCurrency(
                    code: $data['code'],
                    name: $data['name'],
                    symbol: $data['symbol'],
                    decimals: $data['decimals'],
                    type: $data['type'],
                );
            }
        }
    }
}
