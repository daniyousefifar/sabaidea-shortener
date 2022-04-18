<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Link;

use Psr\Http\Message\ResponseInterface as Response;

class ListLinksController extends LinkController
{
    protected function action(): Response
    {
        $user_id = (int) $this->request->getAttribute('token')['uid'];
        $links = $this->linkRepository->findAllByUserId($user_id);

        $this->logger->info("Links list for user {$user_id} was viewed.");

        return $this->respondWithData($links);
    }
}