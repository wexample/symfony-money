<?php

namespace Wexample\SymfonyMoney\Service\Traits;

use App\Wex\BaseBundle\Entity\Traits\PricedEntityTrait;
use Doctrine\ORM\EntityManagerInterface;

trait PricedEntityServiceTrait
{
    abstract public function getEntityManager(): EntityManagerInterface;

    /**
     * @param $entity PricedEntityTrait
     */
    public function pricedEntityUpdate($entity)
    {
        // Save computed total.
        $entity->setPriceTotal($entity->getPriceOverriddenOrCalcPriceTotal());
    }

    public function pricedCloneFields(): array
    {
        return [
            'price_currency',
            'price_discount',
            'price_discount_unit',
            'price_overridden',
            'price_raw',
            'price_total',
            'price_vat',
        ];
    }
}
