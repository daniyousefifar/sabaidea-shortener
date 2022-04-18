<?php

declare(strict_types=1);

use App\Helpers\Shorty;
use Src\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        Shorty::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $shortenerSettings = $settings->get('shortener');

            $chars = $shortenerSettings['characters'];
            $salt = $shortenerSettings['salt'];
            $padding = $shortenerSettings['padding'];

            return new Shorty($chars, $salt, $padding);
        },
    ]);
};