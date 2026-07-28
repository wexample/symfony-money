<?php

namespace Wexample\SymfonyMoney\Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyApi\Api\Class\ApiResponse;
use Wexample\SymfonyApi\Api\Controller\AbstractApiController;
use Wexample\SymfonyMoney\Service\CurrencyService;

#[Route(path: 'api/currency/', name: 'api_currency_')]
class CurrencyController extends AbstractApiController
{
    final public const ROUTE_IMPORT = 'import';

    #[Route(path: 'import', name: self::ROUTE_IMPORT, methods: [Request::METHOD_POST])]
    public function import(CurrencyService $currencyService): ApiResponse
    {
        $currencyService->seed();

        return self::apiResponseSuccess();
    }
}
