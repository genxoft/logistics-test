<?php

declare(strict_types=1);

namespace App\DeliveryService\FastDelivery;

use App\DeliveryService\ItemInterface;
use App\DeliveryService\RequestAdapterInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class RequestAdapter implements RequestAdapterInterface
{
    public const REQUEST_URI = "http://mock-service:8090/fast.php";

    public function __construct(
        private readonly ItemInterface $item
    ) {
    }

    public function request(): RequestInterface
    {
        $uri = new Uri(self::REQUEST_URI);
        $uri->withQuery(http_build_query([
            'sourceKladr'   => $this->item->getFrom(),
            'targetKladr'   => $this->item->getTo(),
            'weight'        => $this->item->getWeight(),
        ]));
        return new Request("GET", $uri);
    }
}
