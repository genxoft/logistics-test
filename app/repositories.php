<?php

declare(strict_types=1);

use App\Domain\Kladr\DummyKladrRepository;
use App\Domain\Kladr\KladrRepositoryInterface;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        KladrRepositoryInterface::class => \DI\autowire(DummyKladrRepository::class),
    ]);
};
