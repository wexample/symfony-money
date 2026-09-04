<?php

declare(strict_types=1);

namespace Wexample\SymfonyMoney\Service\FormProcessor\DataResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Wexample\SymfonyForms\Service\FormProcessor\FormProcessorDataResolverInterface;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;

class CurrencyFormDataResolver implements FormProcessorDataResolverInterface
{
    private const string ROUTE_PARAM_ID = 'id';

    public function __construct(
        private readonly CurrencyRepository $currencyRepository
    ) {
    }

    public function resolve(
        Request $request,
        array $options = []
    ): mixed {
        $id = $request->attributes->get(self::ROUTE_PARAM_ID);

        $currency = $this->currencyRepository->findOneBy([
            self::ROUTE_PARAM_ID => $id,
        ]);

        if (! $currency) {
            throw new NotFoundHttpException('Currency not found: ' . $id);
        }

        return $currency;
    }
}
