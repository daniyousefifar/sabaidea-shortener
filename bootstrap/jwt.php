<?php

declare(strict_types=1);

use App\Exceptions\UnauthorizedException;
use Src\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Tuupola\Middleware\JwtAuthentication;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        JwtAuthentication::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $jwtSettings = $settings->get('jwt');
            $jwt = new JwtAuthentication([
                'secret' => $jwtSettings['secret'],
                'algorithm' => $jwtSettings['algorithm'],
                'secure' => $jwtSettings['secure'],
                'header' => $jwtSettings['header'],
                'ignore' => $jwtSettings['ignore'],
                'error' => function ($response, $args) {
                    throw new UnauthorizedException($args['message']);
                }
            ]);

            return $jwt;
        },
    ]);
};