<?php

declare(strict_types=1);

namespace App\Components\DeliveryService;

class Composition implements ItemInterface, \JsonSerializable {

    private string $from;
    private string $to;
    private float $weight = 0.0;

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
            throw new \InvalidArgumentException("Items can`t be empty");
        }

        foreach ($items as $item) {
            if ($from === null) {
                $from = $item->getFrom();
            } else if ($from !== $item->getFrom()) {
                throw new \InvalidArgumentException("From might be equal for all items");
            }
            if ($to === null) {
                $to = $item->getTo();
            } else if ($to !== $item->getTo()) {
                throw new \InvalidArgumentException("To might be equal for all items");
            }
            $this->weight += $item->getWeight();
        }

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
    public function getItems(): array {
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
