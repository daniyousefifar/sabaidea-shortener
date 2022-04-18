<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $payload = json_encode([
            'message' => "I'm what I seek!",
        ]);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    // API Routes...
    $app->group('/api', function (Group $group) {
        // API v1 Routes...
        $group->group('/v1', function (Group $group) {
            // Authentication Routes...
            $group->post('/auth/login', \App\Controllers\Api\v1\Auth\LoginController::class);

            // Links Routes...
            $group->get('/links', \App\Controllers\Api\v1\Link\ListLinksController::class);
            $group->post('/links', \App\Controllers\Api\v1\Link\CreateLinkController::class);
            $group->get('/links/{id}', \App\Controllers\Api\v1\Link\ViewLinkController::class);
            $group->put('/links/{id}', \App\Controllers\Api\v1\Link\UpdateLinkController::class);
            $group->delete('/links/{id}', \App\Controllers\Api\v1\Link\DeleteLinkController::class);

            // Domains Routes...
            $group->get('/domains', \App\Controllers\Api\v1\Domain\ListDomainsController::class);
            $group->post('/domains', \App\Controllers\Api\v1\Domain\CreateDomainController::class);
            $group->get('/domains/{id}', \App\Controllers\Api\v1\Domain\ViewDomainController::class);
            $group->put('/domains/{id}', \App\Controllers\Api\v1\Domain\UpdateDomainController::class);
            $group->delete('/domains/{id}', \App\Controllers\Api\v1\Domain\DeleteDomainController::class);
        });
    })->addMiddleware($app->getContainer()->get(JwtAuthentication::class));

    $app->get('/{code:[a-zA-Z0-9]+}', \App\Controllers\LinkHandlerController::class);
};

