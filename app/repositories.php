<?php

declare(strict_types=1);

use App\Domain\Kladr\DummyKladrRepository;
use App\Domain\Kladr\KladrRepositoryInterface;
use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        KladrRepositoryInterface::class => autowire(DummyKladrRepository::class),
    ]);
};
