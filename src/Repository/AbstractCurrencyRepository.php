<?php

namespace Wexample\SymfonyMoney\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyMoney\Entity\AbstractCurrency;

/**
 * @method AbstractCurrency|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractCurrency|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractCurrency|null findOneByCode(string $code)
 * @method AbstractCurrency[]    findAll()
 * @method AbstractCurrency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractCurrencyRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $registry,
        $entityClass = AbstractCurrency::class
    ) {
        parent::__construct($registry, $entityClass);
    }

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
