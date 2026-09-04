<?php

namespace Wexample\SymfonyMoney\Api\Normalizer\Entity\Currency;

use ArrayObject;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Interface\NormalizableDataInterface;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;
use Wexample\SymfonyMoney\Api\Dto\PublicCurrencyDto;
use Wexample\SymfonyMoney\Entity\Currency;
use Wexample\SymfonyMoney\Entity\Traits\Manipulator\CurrencyEntityManipulatorTrait;

class DefaultCurrencyNormalizer extends AbstractEntityNormalizer
{
    use CurrencyEntityManipulatorTrait;

    public function normalizeEntity(
        Currency|AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|NormalizableDataInterface|null {
        return PublicCurrencyDto::fromEntity($entity);
    }
}
