<?php

declare(strict_types=1);

namespace App\DeliveryService;

use InvalidArgumentException;
use JsonSerializable;

class Composition implements ItemInterface, JsonSerializable
{
    private readonly string $from;
    private readonly string $to;
    private readonly float $weight;

    /**
     * @param ItemInterface[] $items
     */
    public function __construct(
        private readonly array $items
    ) {
        /** @var string|null $from */
        $from = null;
        /** @var string|null $to */
        $to = null;

        if (count($items) < 1) {
            throw new InvalidArgumentException("Items can`t be empty");
        }
        $weight = 0.0;
        foreach ($items as $item) {
            if ($from === null) {
                $from = $item->getFrom();
            } elseif ($from !== $item->getFrom()) {
                throw new InvalidArgumentException("From might be equal for all items");
            }
            if ($to === null) {
                $to = $item->getTo();
            } elseif ($to !== $item->getTo()) {
                throw new InvalidArgumentException("To might be equal for all items");
            }
            $weight += $item->getWeight();
        }

        $this->weight = $weight;
        $this->from = $from;
        $this->to = $to;
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

    /**
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }


    public function jsonSerialize(): array
    {
        return [
            'from'      => $this->getFrom(),
            'to'        => $this->getTo(),
            'weight'    => $this->getWeight(),
            'items'     => $this->getItems(),
        ];
    }
}
