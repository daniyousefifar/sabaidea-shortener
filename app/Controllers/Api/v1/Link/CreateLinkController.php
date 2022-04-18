<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Link;

use App\Helpers\Shorty;
use Psr\Http\Message\ResponseInterface as Response;
use Selective\Validation\Exception\ValidationException;
use Selective\Validation\Factory\CakeValidationFactory;
use Src\Actions\ActionError;

class CreateLinkController extends LinkController
{
    protected function action(): Response
    {
        $formData = $this->getFormData();
        $formData['user_id'] = (int) $this->request->getAttribute('token')['uid'];

        $validationFactory = new CakeValidationFactory();
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

        if (!$result = $this->linkRepository->create($formData)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $shorty = $this->container->get(Shorty::class);
        $code = $shorty->encode((int)$result);
        if (!$this->linkRepository->storeCode((int)$result, $code)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $this->logger->info("New Link created with {$result} ID.");

        return $this->respondWithData([
            'message' => 'The link was created successfully.',
            'code' => $code,
        ], 201);
    }
}