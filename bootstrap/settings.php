<?php

declare(strict_types=1);

use Src\Settings\Settings;
use Src\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production,
                'logError' => true,
                'logErrorDetails' => true,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'db' => [
                    'host' => 'localhost',
                    'username' => 'admin',
                    'password' => 'admin',
                    'database' => 'shortener',
                    'charset' => 'utf8mb4',
                    'flags' => [
                        // Turn off persistent connections
                        PDO::ATTR_PERSISTENT => false,
                        // Enable exceptions
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Emulate prepared statements
                        PDO::ATTR_EMULATE_PREPARES => true,
                        // Set default fetch mode to array
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ],
                ],
                'shortener' => [
                    'characters' => 'u7oIws3pVWZMQjA4XhNtyvglkEer1C2J5YdT6zLiFm0ObPc8S9KaDHqRBnfUGx',
                    'salt' => 'snAkADdtU4bT3agBzyhhyNKkVXBOEAcA',
                    'padding' => 6,
                ],
                'jwt' => [
                    'secret' => 'hello_world',
                    'algorithm' => 'HS256',
                    'secure' => false,
                    'header' => 'X-API-Token',
                    'ignore' => [
                        '/api/v1/auth/login'
                    ]
                ]
            ]);
        }
    ]);
};