<?php

namespace Wexample\SymfonyMoney\Service\FormProcessor\Traits;

use Symfony\Component\Form\FormInterface;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Service\Entity\EntityNeutralService;

trait PricedFormProcessorTrait
{
    protected function pricedOnSubmitted(
        AbstractEntityInterface $entity,
        FormInterface $form
    ) {
        $fieldSwitch = $form->has('priceTotalOverrideSwitch') ? $form->get('priceTotalOverrideSwitch') : null;

        if (!$fieldSwitch || !$fieldSwitch->getData()) {
            $entity->setPriceOverridden(null);
        }

        // Save computed price.
        $this->getEntityService()->pricedEntityUpdate($entity);
    }

    abstract protected function getEntityService(): EntityNeutralService;
}
