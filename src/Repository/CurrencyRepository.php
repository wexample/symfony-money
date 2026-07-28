<?php

namespace Wexample\SymfonyMoney\Repository;

use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyMoney\Entity\Currency;
use Wexample\SymfonyMoney\Entity\Traits\Manipulator\CurrencyEntityManipulatorTrait;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency|null findOneByCode(string $code)
 * @method Currency|null saveNewCurrency(string $code, string $name, string $symbol, int $decimals, string $type)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends AbstractRepository
{
    use CurrencyEntityManipulatorTrait;

    public function createNewCurrency(
        string $code,
        string $name,
        string $symbol,
        int $decimals,
        string $type,
    ): Currency {
        $currency = new Currency();
        $currency->setCode($code)
            ->setName($name)
            ->setSymbol($symbol)
            ->setDecimals($decimals);
        $currency->setType($type);

        return $currency;
    }

    public function findByCode(string $code): ?Currency
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * @return Currency[]
     */
    public function findAllByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }
}
