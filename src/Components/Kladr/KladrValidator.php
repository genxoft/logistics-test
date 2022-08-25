<?php

declare(strict_types=1);

namespace App\Components\Kladr;

use App\Domain\Kladr\KladrRepositoryInterface;

class KladrValidator
{

    public function __construct(
        private readonly KladrRepositoryInterface $repository
    ) {}

    public function validate(string $code): bool
    {
        return $this->repository->exists($code);
    }
}
