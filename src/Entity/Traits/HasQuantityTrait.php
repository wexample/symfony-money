<?php

namespace Wexample\SymfonyMoney\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

/**
 * An int quantity, scaled by getQuantityScale() (1 by default: plain units).
 * On a priced entity, changing it refreshes the price. Implements QuantifiedInterface.
 */
trait HasQuantityTrait
{
    #[Column(type: Types::INTEGER)]
    protected ?int $quantity = null;

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        if (method_exists($this, 'updatePriceTotal')) {
            $this->updatePriceTotal();
        }

        return $this;
    }

    public function getQuantityScale(): int
    {
        return 1;
    }

    public function increaseQuantity(int $count = 1): int
    {
        $this->setQuantity(($this->quantity ?? 0) + $count * $this->getQuantityScale());

        return $this->quantity;
    }

    public function decreaseQuantity(int $count = 1): int
    {
        return $this->increaseQuantity(-$count);
    }
}
