<?php

declare(strict_types=1);

namespace App\Application\Actions\Health;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class HealthAction extends Action
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
        $this->logger->info("Health was requested.");

        return $this->respondWithData([
            "status" => "pass",
        ], 'application/health+json');
    }
}
