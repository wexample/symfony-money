<?php

namespace Wexample\SymfonyMoney\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasDecimalsTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasSecureIdTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasTypeTrait;
use Wexample\SymfonyMoney\Entity\Traits\HasCurrencyCodeTrait;
use Wexample\SymfonyMoney\Entity\Traits\HasCurrencySymbolTrait;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table(name: 'currency')]
#[PseudocodeExport(inherited: true)]
class Currency extends AbstractEntity
{
    use HasCurrencyCodeTrait;
    use HasCurrencySymbolTrait;
    use HasDecimalsTrait;
    use HasNameTrait;
    use HasSecureIdTrait;
    use HasTypeTrait;

    public const TYPE_CRYPTO = 'crypto';
    public const TYPE_FIAT = 'fiat';

    public static function getSecureIdPrefix(): ?string
    {
        return 'cry';
    }

    public static function getAllowedTypes(): ?array
    {
        return [
            self::TYPE_CRYPTO,
            self::TYPE_FIAT,
        ];
    }
}
