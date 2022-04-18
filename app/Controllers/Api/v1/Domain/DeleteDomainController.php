<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Domain;

use App\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Actions\ActionError;

class DeleteDomainController extends DomainController
{
    protected function action(): Response
    {
        $userID = (int) $this->request->getAttribute('token')['uid'];
        $domainID = (int) $this->resolveArg('id');
        $domain = $this->domainRepository->findDomainById($domainID);

        if ($domain['user_id'] != $userID) {
            throw new UnauthorizedException('Sorry, you are not allowed to perform this operation.', 403);
        }

        if (!$this->domainRepository->delete($domainID)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $this->logger->info("Domain of id `{$domainID}` was deleted.");

        return $this->respondWithData([
            'message' => 'The domain was deleted successfully.',
        ], 200);
    }
}