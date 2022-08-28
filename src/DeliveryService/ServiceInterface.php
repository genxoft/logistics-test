<?php

declare(strict_types=1);

namespace App\DeliveryService;

use Psr\Http\Message\ResponseInterface;

interface ServiceInterface
{
    public function getComposer(): ComposerInterface;

    public function getRequestAdapter(ItemInterface $item): RequestAdapterInterface;

    public function getResponseAdapter(ResponseInterface $response): ResponseAdapterInterface;
}
