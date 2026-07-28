<?php

namespace Wexample\SymfonyMoney\Repository;

use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyMoney\Entity\Currency;
use Wexample\SymfonyMoney\Entity\Traits\Manipulator\CurrencyEntityManipulatorTrait;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency|null findOneByCurrencyCode(string $currencyCode)
 * @method Currency|null saveNewCurrency(string $currencyCode, string $name, string $currencySymbol, int $decimals, string $type)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends AbstractRepository
{
    use CurrencyEntityManipulatorTrait;

    public function createNewCurrency(
        string $currencyCode,
        string $name,
        string $currencySymbol,
        int $decimals,
        string $type,
    ): Currency {
        $currency = new Currency();
        $currency->setCurrencyCode($currencyCode)
            ->setName($name)
            ->setCurrencySymbol($currencySymbol)
            ->setDecimals($decimals);
        $currency->setType($type);

        return $currency;
    }

    public function findByCurrencyCode(string $currencyCode): ?Currency
    {
        return $this->findOneBy(['currencyCode' => $currencyCode]);
    }

    /**
     * @return Currency[]
     */
    public function findAllByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }
}
