<?php

declare(strict_types=1);

namespace App\Application\Actions\Delivery;

use App\Application\Actions\Action;
use App\Components\DeliveryService\DeliveryInfo;
use App\Components\DeliveryService\FastDelivery\Service as FastService;
use App\Components\DeliveryService\Item;
use App\Components\DeliveryService\ItemInterface;
use App\Components\DeliveryService\ServiceInterface;
use App\Components\DeliveryService\SlowDelivery\Service as SlowService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class DeliveryAction extends Action
{

    const WAITING_TIMEOUT = 30;

    /**
     * @var ServiceInterface[]
     */
    private array $services = [];

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
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($this->request, "Invalid data format", $e);
        }
        $client = new Client([
            RequestOptions::TIMEOUT => 15,
        ]);
        $requests = [];
        $services = [];
        $cargos = [];
        foreach ($this->services as $serviceName => $service) {
            $composer = $service->getComposer();
            $blocks = $composer->compose($items);
            foreach ($blocks as $block) {
                $cargos[] = $block;

                $requestAdapter = $service->getRequestAdapter($block);
                $requests[] = $requestAdapter->request();
                $services[] = $serviceName;
            }
        }

        $results = [];
        $pool = new Pool($client, $requests, [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) use ($services, &$results, &$cargos) {
                /** @var ServiceInterface $service */
                $service = $this->services[$services[$index]];
                $responseAdapter = $service->getResponseAdapter($response);
                $deliveryInfo = $responseAdapter->parse()->withCargo($cargos[$index]);
                $results[$services[$index]][] = $deliveryInfo;

            },
            'rejected' => function (TransferException $reason, $index) use ($services, &$results) {
                $results[$services[$index]][] = new DeliveryInfo(0.0, "", $reason->getMessage());
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $this->respondWithData($results);
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
