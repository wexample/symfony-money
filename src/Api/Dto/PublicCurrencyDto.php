<?php

namespace Wexample\SymfonyMoney\Api\Dto;

use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyMoney\Entity\Currency;

class PublicCurrencyDto extends AbstractEntityDto
{
    public string $secureId;

    public string $currencyCode;

    public string $currencySymbol;

    public int $decimals;

    public ?string $name;

    public ?string $type;

    /**
     * @param Currency $entity
     * @return self
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->currencyCode = $entity->getCurrencyCode();
        $dto->currencySymbol = $entity->getCurrencySymbol();
        $dto->decimals = $entity->getDecimals();
        $dto->name = $entity->getName();
        $dto->type = $entity->getType();

        return $dto;
    }
}
