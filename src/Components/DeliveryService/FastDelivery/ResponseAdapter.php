<?php

namespace App\Components\DeliveryService\FastDelivery;

use App\Components\DeliveryService\DeliveryInfo;
use App\Components\DeliveryService\ResponseAdapterInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseAdapter implements ResponseAdapterInterface
{

    public function __construct(
        private readonly ResponseInterface $response
    ) {
    }

    public function parse(): DeliveryInfo
    {
        $rawData = json_decode($this->response->getBody()->getContents(), true);
        if ($rawData === null) {
            return new DeliveryInfo(0, "", "Service returns invalid response");
        }

        if (array_key_exists('error', $rawData) && !empty($rawData['error'])) {
            return new DeliveryInfo(0, "", $rawData['error']);
        }

        if (array_key_exists('price', $rawData)) {
            $price = (float)$rawData['price'];
        } else {
            return new DeliveryInfo(0, "", "Service returns invalid response");
        }

        if (array_key_exists('period', $rawData)) {
            $todayFactor = (int)date("H", time()) > 18 ? 1 : 0;
            $days = (int)$rawData['period'] + $todayFactor;
            $date = new \DateTime('now');
            $date->add(new \DateInterval("P{$days}D"));
        } else {
            return new DeliveryInfo(0, "", "Service returns invalid response");
        }

        return new DeliveryInfo($price, $date->format("Y-m-d"), null);

    }
}
