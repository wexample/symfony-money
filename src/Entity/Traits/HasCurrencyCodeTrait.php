<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasCurrencyCodeTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 10, unique: true)]
    protected string $currencyCode;

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): static
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }
}
