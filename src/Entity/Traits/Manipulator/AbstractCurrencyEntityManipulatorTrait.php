<?php

namespace Wexample\SymfonyMoney\Entity\Traits\Manipulator;

use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyMoney\Entity\AbstractCurrency;

trait AbstractCurrencyEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return AbstractCurrency::class;
    }
}
