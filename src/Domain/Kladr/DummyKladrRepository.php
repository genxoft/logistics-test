<?php

namespace App\Domain\Kladr;

use Psr\Http\Message\RequestInterface;

class DummyKladrRepository implements KladrRepositoryInterface
{
    public function exists(string $code): bool
    {
        return true;
    }
}
