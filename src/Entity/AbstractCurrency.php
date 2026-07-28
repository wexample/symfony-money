<?php

namespace Wexample\SymfonyMoney\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasTypeTrait;

abstract class AbstractCurrency extends AbstractEntity
{
    use HasNameTrait;
    use HasTypeTrait;

    public const TYPE_CRYPTO = 'crypto';
    public const TYPE_FIAT = 'fiat';

    #[ORM\Column(type: Types::STRING, length: 10, unique: true)]
    protected string $code;

    #[ORM\Column(type: Types::SMALLINT)]
    protected int $decimals = 2;

    #[ORM\Column(type: Types::STRING, length: 10)]
    protected string $symbol;

    public static function getAllowedTypes(): ?array
    {
        return [
            self::TYPE_CRYPTO,
            self::TYPE_FIAT,
        ];
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }
}
