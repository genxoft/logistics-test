<?php

declare(strict_types=1);

namespace App\DeliveryService;

use JsonSerializable;

class DeliveryInfo implements JsonSerializable
{
    /**
     * @var ItemInterface|null
     */
    private ?ItemInterface $cargo = null;

    public function __construct(
        private readonly float $price,
        private readonly string $date,
        private readonly ?string $error,
    ) {
    }

    /**
     * @param ItemInterface $cargo
     * @return $this
     */
    public function withCargo(ItemInterface $cargo): static
    {
        $this->cargo = $cargo;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getCargo(): ?ItemInterface
    {
        return $this->cargo;
    }

    public function jsonSerialize(): array
    {
        return [
            'price' => $this->getPrice(),
            'date'  => $this->getDate(),
            'error' => $this->getError(),
            'cargo' => $this->getCargo(),
        ];
    }
}
