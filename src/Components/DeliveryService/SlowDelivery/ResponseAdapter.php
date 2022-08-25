<?php

namespace App\Components\DeliveryService\SlowDelivery;

use App\Components\DeliveryService\DeliveryInfo;
use App\Components\DeliveryService\ResponseAdapterInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseAdapter implements ResponseAdapterInterface
{

    const BASIC_PRICE = 150;

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

        if (array_key_exists('coefficient', $rawData)) {
            $price = self::BASIC_PRICE * (float)$rawData['coefficient'];
        } else {
            return new DeliveryInfo(0, "", "Service returns invalid response");
        }

        if (array_key_exists('date', $rawData)) {
            $date = $rawData['date'];
        } else {
            return new DeliveryInfo(0, "", "Service returns invalid response");
        }

        return new DeliveryInfo($price, $date, null);
    }
}
