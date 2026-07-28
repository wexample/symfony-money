<?php

namespace Wexample\SymfonyMoney\Service;

use Doctrine\ORM\EntityManagerInterface;
use Wexample\SymfonyHelpers\Service\Entity\AbstractEntityService;
use Wexample\SymfonyMoney\Data\CurrencyData;
use Wexample\SymfonyMoney\Entity\Traits\Manipulator\AbstractCurrencyEntityManipulatorTrait;
use Wexample\SymfonyMoney\Repository\AbstractCurrencyRepository;

class CurrencyService extends AbstractEntityService
{
    use AbstractCurrencyEntityManipulatorTrait;

    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly AbstractCurrencyRepository $currencyRepository,
    ) {
        parent::__construct($entityManager);
    }

    public function seed(): void
    {
        foreach (CurrencyData::getAll() as $data) {
            $currency = $this->currencyRepository->findByCode($data['code']);

            if ($currency) {
                $currency
                    ->setName($data['name'])
                    ->setSymbol($data['symbol'])
                    ->setDecimals($data['decimals'])
                    ->setType($data['type']);

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
