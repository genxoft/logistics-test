<?php

declare(strict_types=1);

namespace App\Application\Actions\Delivery;

use App\Application\Actions\Action;
use App\DeliveryService\CalcDelivery;
use App\DeliveryService\DeliveryInfo;
use App\DeliveryService\FastDelivery\Service as FastService;
use App\DeliveryService\Item;
use App\DeliveryService\ItemInterface;
use App\DeliveryService\ServiceInterface;
use App\DeliveryService\SlowDelivery\Service as SlowService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class DeliveryAction extends Action
{
    /**
     * @var ServiceInterface[]
     */
    private array $services;

    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);

        // Init allowed services
        $this->services = [
            'fast' => new FastService(),
            'slow' => new SlowService(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        try {
            $items = $this->loadItems($data);
        } catch (InvalidArgumentException $e) {
            throw new HttpBadRequestException($this->request, "Invalid data format", $e);
        }

        $deliveryCalc = (new CalcDelivery($this->logger))
            ->withServices($this->services)
            ->withItems($items);

        return $this->respondWithData($deliveryCalc->calculate());
    }

    /**
     * @internal
     * @param array $data
     * @return ItemInterface[]
     */
    private function loadItems(array $data): array
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = Item::fromArray($item);
        }
        return $result;
    }
}
