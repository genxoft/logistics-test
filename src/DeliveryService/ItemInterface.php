<?php

declare(strict_types=1);

namespace App\DeliveryService;

interface ItemInterface
{
    public function getFrom(): string;

    public function getTo(): string;

    public function getWeight(): float;
}
