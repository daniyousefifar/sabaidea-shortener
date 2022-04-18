<?php

declare(strict_types=1);

use Src\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $dbSettings = $settings->get('db');

            $host = $dbSettings['host'];
            $dbname = $dbSettings['database'];
            $username = $dbSettings['username'];
            $password = $dbSettings['password'];
            $charset = $dbSettings['charset'];

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

            return new PDO($dsn, $username, $password);
        },
    ]);
};