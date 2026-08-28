<?php

namespace Wexample\SymfonyMoney\Service;

use Wexample\SymfonyMoney\Data\CurrencyData;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;

class CurrencyService
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
    ) {
    }

    public function seed(): void
    {
        foreach (CurrencyData::getAll() as $data) {
            $currency = $this->currencyRepository->findByCurrencyCode($data['code']);

            if ($currency) {
                $currency->setName($data['name'])
                    ->setCurrencySymbol($data['symbol'])
                    ->setDecimals($data['decimals']);
                $currency->setType($data['type']);

                $this->currencyRepository->save($currency);
            } else {
                $this->currencyRepository->saveNewCurrency(
                    currencyCode: $data['code'],
                    name: $data['name'],
                    currencySymbol: $data['symbol'],
                    decimals: $data['decimals'],
                    type: $data['type'],
                );
            }
        }
    }
}
