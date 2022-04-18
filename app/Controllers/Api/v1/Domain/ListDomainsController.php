<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Domain;

use Psr\Http\Message\ResponseInterface as Response;

class ListDomainsController extends DomainController
{
    protected function action(): Response
    {
        $user_id = (int) $this->request->getAttribute('token')['uid'];
        $domains = $this->domainRepository->findAllByUserId($user_id);

        $this->logger->info("Domains list for user {$user_id} was viewed.");

        return $this->respondWithData($domains);
    }
}