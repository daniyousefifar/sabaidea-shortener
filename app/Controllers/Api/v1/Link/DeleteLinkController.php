<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Link;

use App\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Actions\ActionError;

class DeleteLinkController extends LinkController
{
    protected function action(): Response
    {
        $userID = (int) $this->request->getAttribute('token')['uid'];
        $linkID = (int) $this->resolveArg('id');
        $link = $this->linkRepository->findLinkById($linkID);

        if ($link['user_id'] != $userID) {
            throw new UnauthorizedException('Sorry, you are not allowed to perform this operation.', 403);
        }

        if (!$this->linkRepository->delete($linkID)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $this->logger->info("Link of id `{$linkID}` was deleted.");

        return $this->respondWithData([
            'message' => 'The link was deleted successfully.',
        ], 200);
    }
}