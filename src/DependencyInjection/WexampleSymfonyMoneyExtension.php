<?php

namespace Wexample\SymfonyMoney\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;

class WexampleSymfonyMoneyExtension extends AbstractWexampleSymfonyExtension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ) {
        $this->loadConfig(
            __DIR__,
            $container
        );
    }
}
