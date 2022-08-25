<?php

declare(strict_types=1);

namespace App\Components\DeliveryService;

use InvalidArgumentException;

class Item implements ItemInterface, \JsonSerializable
{
    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly float $weight,
    ) {
        if ($this->weight <= 0) {
            throw new InvalidArgumentException("Weight might be more than 0");
        }
    }

    public static function fromArray(array $data): static
    {
        if (
            !array_key_exists('from', $data) ||
            !array_key_exists('to', $data) ||
            !array_key_exists('weight', $data)
        ) {
            throw new InvalidArgumentException("Data array invalid");
        }

        return new static((string)$data['from'], (string)$data['to'], (float)$data['weight']);
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function jsonSerialize(): array
    {
        return [
            'from'      => $this->getFrom(),
            'to'        => $this->getTo(),
            'weight'    => $this->getWeight(),
        ];
    }
}
