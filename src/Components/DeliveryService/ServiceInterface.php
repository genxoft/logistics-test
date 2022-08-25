<?php

namespace App\Components\DeliveryService;

use Psr\Http\Message\ResponseInterface;

interface ServiceInterface
{
    public function getMaxWeight(): ?float;

    public function getComposer(): ComposerInterface;

    public function getRequestAdapter(ItemInterface $item): RequestAdapterInterface;

    public function getResponseAdapter(ResponseInterface $response): ResponseAdapterInterface;
}
