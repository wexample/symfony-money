<?php

namespace Wexample\SymfonyMoney\Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyApi\Api\Class\ApiResponse;
use Wexample\SymfonyApi\Api\Controller\AbstractApiController;
use Wexample\SymfonyHelpers\Controller\AbstractController;
use Wexample\SymfonyMoney\Api\Normalizer\Entity\Currency\DefaultCurrencyNormalizer;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;
use Wexample\SymfonyMoney\Service\CurrencyService;

#[Route(path: 'api/currency/', name: 'api_currency_')]
class CurrencyController extends AbstractApiController
{
    final public const ROUTE_IMPORT = 'import';
    final public const ROUTE_LIST = 'list';

    #[Route(path: 'import', name: self::ROUTE_IMPORT, methods: [Request::METHOD_POST], options: AbstractController::ROUTE_OPTIONS_ONLY_EXPOSE)]
    public function import(CurrencyService $currencyService): ApiResponse
    {
        $currencyService->seed();

        return self::apiResponseSuccess();
    }

    #[Route(path: 'list', name: self::ROUTE_LIST, methods: AbstractController::ROUTE_OPTIONS_METHOD_ONLY_GET, options: AbstractController::ROUTE_OPTIONS_ONLY_EXPOSE)]
    public function list(
        CurrencyRepository $currencyRepository,
        DefaultCurrencyNormalizer $normalizer,
    ): ApiResponse {
        return self::apiResponseCollection(
            $normalizer->normalizeCollection($currencyRepository->findAll())
        );
    }
}
