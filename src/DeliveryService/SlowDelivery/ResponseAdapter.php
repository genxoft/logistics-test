<?php

declare(strict_types=1);

namespace App\DeliveryService\SlowDelivery;

use App\DeliveryService\DeliveryInfo;
use App\DeliveryService\ResponseAdapterInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ResponseAdapter implements ResponseAdapterInterface
{
    public const BASIC_PRICE = 150;

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

        if (array_key_exists('coefficient', $rawData)) {
            $price = self::BASIC_PRICE * (float)$rawData['coefficient'];
        } else {
            throw new RuntimeException("Field coefficient not found in response");
        }

        if (array_key_exists('date', $rawData)) {
            $date = $rawData['date'];
        } else {
            throw new RuntimeException("Field date not found in response");
        }

        return new DeliveryInfo($price, $date, null);
    }
}
