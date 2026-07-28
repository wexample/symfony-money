<?php

namespace Wexample\SymfonyMoney\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;

class WexampleSymfonyMoneyExtension extends AbstractWexampleSymfonyExtension implements PrependExtensionInterface
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $this->loadConfig(
            __DIR__,
            $container
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'WexampleSymfonyMoney' => [
                        'type' => 'attribute',
                        'dir' => __DIR__ . '/../Entity',
                        'prefix' => 'Wexample\\SymfonyMoney\\Entity',
                        'alias' => 'WexampleSymfonyMoney',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);
    }
}
