<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(file_get_contents(__DIR__ . '/../view/swagger.phtml'));
        return $response;
    });

    $app->group('/docs', function (Group $group) {
        $group->get('/api-yaml', function (Request $request, Response $response) {
            $response->getBody()->write(file_get_contents(__DIR__ . '/../docs/api.yaml'));
            $response->withHeader("Content-type", "application/yaml");
            return $response;
        });
    });

    $app->group('/api', function (Group $group) {
        $group->options('/{routes:.*}', function (Request $request, Response $response) {
            return $response;
        });
        $group->get('/health', \App\Application\Actions\Health\HealthAction::class);
        $group->post('/delivery', \App\Application\Actions\Delivery\DeliveryAction::class);
    });

    $app->group('/mock-delivery', function (Group $group) {
        $group->get('/slow', \App\Application\Actions\MockDelivery\SlowAction::class);
        $group->get('/fast', \App\Application\Actions\MockDelivery\FastAction::class);
    });
};
