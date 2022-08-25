<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Health;

use App\Application\Actions\ActionPayload;
use DI\Container;
use Tests\TestCase;

class HealthActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $request = $this->createRequest('GET', '/api/health');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, ["status" => "pass"]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
