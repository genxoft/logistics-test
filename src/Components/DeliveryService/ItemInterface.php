<?php

namespace App\Components\DeliveryService;

interface ItemInterface
{
    public function getFrom(): string;

    public function getTo(): string;

    public function getWeight(): float;
}
