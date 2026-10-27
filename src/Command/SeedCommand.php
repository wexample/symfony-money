<?php

namespace Wexample\SymfonyMoney\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wexample\SymfonyHelpers\Command\AbstractBundleCommand;
use Wexample\SymfonyHelpers\Service\BundleService;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;
use Wexample\SymfonyMoney\Service\CurrencyService;
use Wexample\SymfonyMoney\WexampleSymfonyMoneyBundle;

/**
 * The currencies of the reference list, written once and updated after: what
 * `_api/currency/import` does, for an application with no one to click it.
 */
class SeedCommand extends AbstractBundleCommand
{
    public function __construct(
        BundleService $bundleService,
        private readonly CurrencyService $currencyService,
        private readonly CurrencyRepository $currencyRepository,
    ) {
        parent::__construct($bundleService);
    }

    public static function getBundleClassName(): string
    {
        return WexampleSymfonyMoneyBundle::class;
    }

    protected function configure(): void
    {
        $this->setDescription('Writes the currencies of the reference list; those already there are updated.');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->currencyService->seed();

        $output->writeln(sprintf('%d currencies.', $this->currencyRepository->count([])));

        return self::SUCCESS;
    }
}
