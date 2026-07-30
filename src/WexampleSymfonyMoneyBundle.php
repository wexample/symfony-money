<?php

namespace Wexample\SymfonyMoney;

use Wexample\SymfonyHelpers\Class\AbstractBundle;
use Wexample\SymfonyHelpers\Interface\LoaderBundleInterface;
use Wexample\SymfonyPseudocode\Interface\PseudocodeBundleInterface;

class WexampleSymfonyMoneyBundle extends AbstractBundle implements PseudocodeBundleInterface, LoaderBundleInterface
{
    public static function getPseudocodeSourcePaths(): array
    {
        return [__DIR__ . '/'];
    }

    public static function getLoaderFrontPaths(): array
    {
        return [__DIR__ . '/../assets/'];
    }
}
