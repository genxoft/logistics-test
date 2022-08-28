<?php

declare(strict_types=1);

namespace App\DeliveryService\FastDelivery;

use App\DeliveryService\DeliveryInfo;
use App\DeliveryService\ResponseAdapterInterface;
use DateInterval;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

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
            throw new RuntimeException("Invalid response format");
        }

        if (array_key_exists('error', $rawData) && !empty($rawData['error'])) {
            throw new RuntimeException("Field error not found in response");
        }

        if (array_key_exists('price', $rawData)) {
            $price = (float)$rawData['price'];
        } else {
            throw new RuntimeException("Field price not found in response");
        }

        if (array_key_exists('period', $rawData)) {
            $todayFactor = (int)date("H", time()) > 18 ? 1 : 0;
            $days = (int)$rawData['period'] + $todayFactor;
            $date = new DateTime('now');
            $date->add(new DateInterval("P{$days}D"));
        } else {
            throw new RuntimeException("Field period not found in response");
        }

        return new DeliveryInfo($price, $date->format("Y-m-d"), null);
    }
}
