<?php

namespace Wexample\SymfonyMoney;

use Wexample\SymfonyHelpers\Class\AbstractBundle;
use Wexample\SymfonyPseudocode\Interface\PseudocodeBundleInterface;

class WexampleSymfonyMoneyBundle extends AbstractBundle implements PseudocodeBundleInterface
{
    public static function getPseudocodeSourcePaths(): array
    {
        return [__DIR__ . '/'];
    }
}
