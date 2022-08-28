<?php

declare(strict_types=1);

namespace App\DeliveryService;

use Psr\Http\Message\RequestInterface;

interface RequestAdapterInterface
{
    public function __construct(ItemInterface $item);

    public function request(): RequestInterface;
}
