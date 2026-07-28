<?php

namespace Wexample\SymfonyMoney\Repository;

use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyMoney\Entity\AbstractCurrency;
use Wexample\SymfonyMoney\Entity\Traits\Manipulator\AbstractCurrencyEntityManipulatorTrait;

/**
 * @method AbstractCurrency|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractCurrency|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractCurrency|null findOneByCode(string $code)
 * @method AbstractCurrency|null saveNewCurrency(string $code, string $name, string $symbol, int $decimals, string $type)
 * @method AbstractCurrency[]    findAll()
 * @method AbstractCurrency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractCurrencyRepository extends AbstractRepository
{
    use AbstractCurrencyEntityManipulatorTrait;

    abstract public function createNewCurrency(
        string $code,
        string $name,
        string $symbol,
        int $decimals,
        string $type,
    ): AbstractCurrency;

    public function findByCode(string $code): ?AbstractCurrency
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * @return AbstractCurrency[]
     */
    public function findAllByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }
}
