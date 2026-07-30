<?php

namespace Wexample\SymfonyMoney\Service\FormProcessor;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Wexample\SymfonyForms\Service\FormProcessor\AbstractFormProcessor;
use Wexample\SymfonyMoney\Entity\Currency;
use Wexample\SymfonyMoney\Form\CurrencyForm;
use Wexample\SymfonyMoney\Repository\CurrencyRepository;

class CurrencyFormProcessor extends AbstractFormProcessor
{
    public function __construct(
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $entityManager,
        private readonly CurrencyRepository $currencyRepository
    ) {
        parent::__construct($formFactory, $requestStack, $urlGenerator);
    }

    public static function getFormClass(): string
    {
        return CurrencyForm::class;
    }

    public function onValid(FormInterface $form): void
    {
        /** @var Currency $currency */
        $currency = $form->getData();

        if ($this->currencyRepository->findByCurrencyCode($currency->getCurrencyCode())) {
            $this->addFormErrorFromApiKey($form, 'ERR_CURRENCY_CODE_ALREADY_USED');

            return;
        }

        $currency->setGeneratedSecureId();

        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        $this->setSuccessAction(['type' => self::ACTION_DEFAULT]);
        $this->setNotification('@form::success.message');
    }
}
