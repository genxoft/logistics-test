<?php

declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    private int $statusCode;

    /**
     * @var array|object|null
     */
    private $data;

    private ?ActionError $error;

    public function __construct(
        int $statusCode = 200,
        $data = null,
        ?ActionError $error = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array|null|object
     */
    public function getData()
    {
        return $this->data;
    }

    public function getError(): ?ActionError
    {
        return $this->error;
    }

    public function jsonSerialize(): array|JsonSerializable
    {
        if ($this->data !== null) {
            return $this->data;
        } elseif ($this->error !== null) {
            return $this->error;
        } else {
            return new ActionError(ActionError::SERVER_ERROR, "Unexpected error");
        }

    }
}
