<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Link;

use App\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Selective\Validation\Exception\ValidationException;
use Selective\Validation\Factory\CakeValidationFactory;
use Src\Actions\ActionError;

class UpdateLinkController extends LinkController
{
    protected function action(): Response
    {
        $formData = $this->getFormData();
        $linkID = (int) $this->resolveArg('id');
        $userID = (int) $this->request->getAttribute('token')['uid'];

        $validationFactory= new CakeValidationFactory();
        $validator = $validationFactory->createValidator();

        $validator
            ->requirePresence('url', true, 'The url field is required.')
            ->notEmptyString('url', 'The url field is required.')
            ->urlWithProtocol('url', 'The url must be a valid URL.')
            ->requirePresence('domain_id', true, 'The domain field is required.')
            ->notEmptyString('domain_id', 'The domain field is required.')
            ->integer('domain_id', 'The domain must be an integer.');

        $validationResult = $validationFactory->createValidationResult(
            $validator->validate($formData)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Validation failed. Please check your input.', $validationResult);
        }

        $link = $this->linkRepository->findLinkById($linkID);
        if ($link['user_id'] != $userID) {
            throw new UnauthorizedException('Sorry, you are not allowed to perform this operation.', 403);
        }

        if (!$result = $this->linkRepository->update($linkID, $formData)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $this->logger->info("Link of id `{$linkID}` was updated.");

        return $this->respondWithData([
            'message' => 'The link was updated successfully.',
        ], 200);
    }
}