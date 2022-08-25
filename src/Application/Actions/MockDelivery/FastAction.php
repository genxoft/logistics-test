<?php

declare(strict_types=1);

namespace App\Application\Actions\MockDelivery;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class FastAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        // Unnecessary for emulation
        $params = $this->request->getQueryParams();

        // Emulate processing 1 - 10 seconds
        // usleep(rand(0, 10000) * 1000);

        return $this->respondWithData([
            "price"     => rand(1000, 10000) / 10, // random 100 - 1000
            "period"    => rand(1, 5), // random 1 - 5 days
            "error"     => rand(0, 100) == 0 ? "some error" : null, // error emulation
        ]);
    }
}
