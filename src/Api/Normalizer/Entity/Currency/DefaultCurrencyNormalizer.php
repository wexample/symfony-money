<?php

namespace Wexample\SymfonyMoney\Api\Normalizer\Entity\Currency;

use ArrayObject;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;
use Wexample\SymfonyMoney\Entity\Currency;
use Wexample\SymfonyMoney\Entity\Traits\Manipulator\CurrencyEntityManipulatorTrait;

class DefaultCurrencyNormalizer extends AbstractEntityNormalizer
{
    use CurrencyEntityManipulatorTrait;

    public function normalizeEntity(
        Currency|AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null {
        return parent::normalizeEntity($entity, $format, $context)
            + [
                'currencyCode' => $entity->getCurrencyCode(),
                'name' => $entity->getName(),
                'type' => $entity->getType(),
                'currencySymbol' => $entity->getCurrencySymbol(),
                'decimals' => $entity->getDecimals(),
            ];
    }
}
