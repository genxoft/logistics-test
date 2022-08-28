<?php

declare(strict_types=1);

namespace App\DeliveryService\FastDelivery;

use App\DeliveryService\ComposerInterface;
use App\DeliveryService\DummyComposer;
use App\DeliveryService\ItemInterface;
use App\DeliveryService\RequestAdapterInterface;
use App\DeliveryService\ResponseAdapterInterface;
use App\DeliveryService\ServiceInterface;
use Psr\Http\Message\ResponseInterface;

class Service implements ServiceInterface
{
    /**
     * Max weight for each package
     */
    public const MAX_WEIGHT = 1;

    public function getComposer(): ComposerInterface
    {
        return new DummyComposer(self::MAX_WEIGHT);
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
