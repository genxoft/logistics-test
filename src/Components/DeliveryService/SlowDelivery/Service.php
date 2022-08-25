<?php

namespace App\Components\DeliveryService\SlowDelivery;

use App\Components\DeliveryService\ComposerInterface;
use App\Components\DeliveryService\DummyComposer;
use App\Components\DeliveryService\ItemInterface;
use App\Components\DeliveryService\RequestAdapterInterface;
use App\Components\DeliveryService\ResponseAdapterInterface;
use App\Components\DeliveryService\ServiceInterface;
use Psr\Http\Message\ResponseInterface;

class Service implements ServiceInterface
{

    public function getMaxWeight(): ?float
    {
        return null;
    }

    public function getComposer(): ComposerInterface
    {
        return new DummyComposer($this->getMaxWeight());
    }

    public function getRequestAdapter(ItemInterface $item): RequestAdapterInterface
    {
        return new RequestAdapter($item);
    }

    public function getResponseAdapter(ResponseInterface $response): ResponseAdapterInterface
    {
        return new ResponseAdapter($response);
    }
}
