<?php

declare(strict_types=1);

use App\Repositories\Database\DomainRepository;
use App\Repositories\Database\LinkRepository;
use App\Repositories\Database\UserRepository;
use App\Repositories\DomainRepositoryInterface;
use App\Repositories\LinkRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use DI\ContainerBuilder;
use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserRepositoryInterface::class => autowire(UserRepository::class),
        LinkRepositoryInterface::class => autowire(LinkRepository::class),
        DomainRepositoryInterface::class => autowire(DomainRepository::class),
    ]);
};