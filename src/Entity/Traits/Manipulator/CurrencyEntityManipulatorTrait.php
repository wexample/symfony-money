<?php

namespace Wexample\SymfonyMoney\Entity\Traits\Manipulator;

use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyMoney\Entity\Currency;

trait CurrencyEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return Currency::class;
    }
}
