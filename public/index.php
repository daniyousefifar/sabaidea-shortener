<?php

declare(strict_types=1);

use Slim\Factory\ServerRequestCreatorFactory;
use Src\Handlers\HttpErrorHandler;
use Src\Handlers\ShutdownHandler;
use Src\ResponseEmitter\ResponseEmitter;
use Src\Settings\SettingsInterface;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false)  { // Should be set to true in production
    $containerBuilder->enableCompilation(__DIR__ . '/../storage/cache');
}

// Set up settings
$settings = require __DIR__ . '/../bootstrap/settings.php';
$settings($containerBuilder);

// Set up logger
$logger = require __DIR__ . '/../bootstrap/logging.php';
$logger($containerBuilder);

// Set up database
$database = require __DIR__ . '/../bootstrap/database.php';
$database($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../bootstrap/repositories.php';
$repositories($containerBuilder);

// Set up shortener
$shortener = require __DIR__ . '/../bootstrap/shortener.php';
$shortener($containerBuilder);

// Set up JWT
$jwt = require __DIR__ . '/../bootstrap/jwt.php';
$jwt($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
// ...

// Register routes
$routes = require __DIR__ . '/../routes/web.php';
$routes($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logError, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
