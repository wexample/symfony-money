<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasCurrencySymbolTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 10)]
    protected string $currencySymbol;

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    public function setCurrencySymbol(string $currencySymbol): static
    {
        $this->currencySymbol = $currencySymbol;

        return $this;
    }
}
