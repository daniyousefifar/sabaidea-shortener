<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Domain;

use App\Helpers\Shorty;
use Psr\Http\Message\ResponseInterface as Response;
use Selective\Validation\Exception\ValidationException;
use Selective\Validation\Factory\CakeValidationFactory;
use Src\Actions\ActionError;

class CreateDomainController extends DomainController
{
    protected function action(): Response
    {
        $formData = $this->getFormData();
        $formData['user_id'] = (int) $this->request->getAttribute('token')['uid'];

        $validationFactory = new CakeValidationFactory();
        $validator = $validationFactory->createValidator();

        $validator
            ->requirePresence('domain', true, 'The domain field is required.')
            ->notEmptyString('domain', 'The domain field is required.')
            ->regex('domain', '/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/', 'The domain must be a valid domain name.');

        $validationResult = $validationFactory->createValidationResult(
            $validator->validate($formData)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Validation failed. Please check your input.', $validationResult);
        }

        if (!$result = $this->domainRepository->create($formData)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $this->logger->info("New Domain created with {$result} ID.");

        return $this->respondWithData([
            'message' => 'The domain was created successfully.',
        ], 201);
    }
}