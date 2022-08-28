<?php

declare(strict_types=1);

namespace App\DeliveryService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Iterator;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

class CalcDelivery
{
    public const REQUEST_CONCURRENCY = 5;

    /**
     * @var null|array {
     *      {
     *          serviceName: string,
     *          service: ServiceInterface,
     *          cargo: Composition,
     *          requestAdapter: RequestAdapter,
     *      }
     * }
     */
    private ?array $preparedRequests = null;

    /**
     * @var null|DeliveryInfo[]
     */
    private ?array $results;

    /**
     * @var array<string, ServiceInterface>
     */
    private array $services = [];

    /**
     * @var ItemInterface[]
     */
    private array $items = [];

    private readonly LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    public function withService(string $name, ServiceInterface $service): static
    {
        if (array_key_exists($name, $this->services)) {
            throw new InvalidArgumentException("Service name already exists");
        }
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * @param ServiceInterface[] $services
     * @return static
     */
    public function withServices(array $services): static
    {
        foreach ($services as $name => $service) {
            if (!is_string($name)) {
                throw new InvalidArgumentException("Key of array must be a string");
            }
            $this->withService($name, $service);
        }
        return $this;
    }

    public function withItem(ItemInterface $item): static
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param ItemInterface[] $items
     * @return $this
     */
    public function withItems(array $items): static
    {
        foreach ($items as $item) {
            $this->withItem($item);
        }
        return $this;
    }

    private function prepareRequests(): void
    {
        $this->preparedRequests = [];
        foreach ($this->services as $name => $service) {
            $composer = $service->getComposer();
            $packages = $composer->compose($this->items);
            foreach ($packages as $cargo) {
                $this->preparedRequests[] = [
                    'serviceName'       => $name,
                    'service'           => $service,
                    'cargo'             => $cargo,
                    'requestAdapter'    => $service->getRequestAdapter($cargo),
                ];
            }
        }
    }

    /**
     * @internal
     * @return Iterator<RequestInterface>
     */
    private function getRequests(): Iterator
    {
        if ($this->preparedRequests === null) {
            throw new LogicException("You need to prepareRequests first");
        }
        foreach ($this->preparedRequests as $request) {
            yield $request['requestAdapter']->request();
        }
    }

    /**
     * @return array
     */
    public function calculate(): array
    {
        $client = new Client([
            RequestOptions::TIMEOUT => 15,
        ]);

        $this->prepareRequests();

        $pool = new Pool($client, $this->getRequests(), [
            'concurrency'   => self::REQUEST_CONCURRENCY,
            'fulfilled'     => $this->onFulfilled(...),
            'rejected'      => $this->onRejected(...),
        ]);

        $promise = $pool->promise();
        $this->results = [];
        $promise->wait();
        return $this->results;
    }

    /**
     * @internal
     */
    private function onFulfilled(Response $response, int $index): void
    {
        $serviceName = $this->preparedRequests[$index]['serviceName'];
        if (!array_key_exists($serviceName, $this->results)) {
            $this->results[$serviceName] = [];
        }
        if (!str_starts_with((string)$response->getStatusCode(), '2')) {
            $this->logger->error(sprintf("Delivery service %s returns %s", $serviceName, $response->getReasonPhrase()));
            $this->results[$serviceName][] = (new DeliveryInfo(0.0, "", "Service unavailable"))
                ->withCargo($this->preparedRequests[$index]['cargo']);
        }

        $service = $this->services[$serviceName];
        $responseAdapter = $service->getResponseAdapter($response);
        try {
            $this->results[$serviceName][] = $responseAdapter->parse()
                ->withCargo($this->preparedRequests[$index]['cargo']);
        } catch (Throwable $e) {
            $this->logger->error(sprintf("Unable to parse response: %s", $e->getMessage()));
            $this->results[$serviceName][] = (new DeliveryInfo(0.0, "", "Service unavailable"))
                ->withCargo($this->preparedRequests[$index]['cargo']);
        }
    }

    /**
     * @internal
     */
    private function onRejected(TransferException $reason, int $index): void
    {
        $serviceName = $this->preparedRequests[$index]['serviceName'];
        if (!array_key_exists($serviceName, $this->results)) {
            $this->results[$serviceName] = [];
        }
        $this->logger->error($reason->getMessage());
        $this->results[$serviceName][] = (new DeliveryInfo(0.0, "", "Service unavailable"))
            ->withCargo($this->preparedRequests[$index]['cargo']);
    }
}
