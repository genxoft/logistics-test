<?php

namespace App\Domain\Kladr;

interface KladrRepositoryInterface
{
    public function exists(string $code): bool;
}
