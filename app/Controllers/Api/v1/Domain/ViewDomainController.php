<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Domain;

use App\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;

class ViewDomainController extends DomainController
{
    protected function action(): Response
    {
        $userID = (int) $this->request->getAttribute('token')['uid'];
        $domainID = (int) $this->resolveArg('id');
        $domain = $this->domainRepository->findDomainById($domainID);

        if ($domain['user_id'] != $userID) {
            throw new UnauthorizedException('Sorry, you are not allowed to perform this operation.', 403);
        }

        $this->logger->info("Domain of id {$domainID} was viewed.");

        return $this->respondWithData($domain);
    }
}