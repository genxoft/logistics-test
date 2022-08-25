<?php

declare(strict_types=1);

namespace App\Components\DeliveryService;

interface ComposerInterface
{
    /**
     * @param ItemInterface[] $items
     * @return Composition[]
     */
    public function compose(array $items): array;
}
