<?php

namespace Wexample\SymfonyMoney\Service;

use Wexample\SymfonyMoney\Data\CurrencyData;
use Wexample\SymfonyMoney\Entity\AbstractCurrency;
use Wexample\SymfonyMoney\Repository\AbstractCurrencyRepository;

abstract class AbstractCurrencySeeder
{
    abstract protected function getRepository(): AbstractCurrencyRepository;

    abstract protected function createCurrency(): AbstractCurrency;

    public function seed(): void
    {
        $repository = $this->getRepository();

        foreach (CurrencyData::getAll() as $data) {
            $currency = $repository->findByCode($data['code']) ?? $this->createCurrency();

            $currency
                ->setCode($data['code'])
                ->setName($data['name'])
                ->setSymbol($data['symbol'])
                ->setDecimals($data['decimals'])
                ->setType($data['type']);

            $repository->save($currency, flush: false);
        }

        $repository->getEntityManager()->flush();
    }
}
