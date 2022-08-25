<?php

declare(strict_types=1);

namespace App\Components\DeliveryService;

use Psr\Http\Message\ResponseInterface;

interface ResponseAdapterInterface
{
    public function __construct(ResponseInterface $response);

    public function parse(): DeliveryInfo;
}
