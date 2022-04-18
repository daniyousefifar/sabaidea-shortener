<?php

declare(strict_types=1);

namespace App\Controllers\Api\v1\Domain;

use App\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Selective\Validation\Exception\ValidationException;
use Selective\Validation\Factory\CakeValidationFactory;
use Src\Actions\ActionError;

class UpdateDomainController extends DomainController
{
    protected function action(): Response
    {
        $formData = $this->getFormData();
        $domainID = (int) $this->resolveArg('id');
        $userID = (int) $this->request->getAttribute('token')['uid'];

        $validationFactory= new CakeValidationFactory();
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

        $domain = $this->domainRepository->findDomainById($domainID);
        if ($domain['user_id'] != $userID) {
            throw new UnauthorizedException('Sorry, you are not allowed to perform this operation.', 403);
        }

        if (!$this->domainRepository->update($domainID, $formData)) {
            return $this->respondWithError('Something went wrong.', ActionError::SERVER_ERROR, 500);
        }

        $this->logger->info("Domain of id `{$domainID}` was updated.");

        return $this->respondWithData([
            'message' => 'The domain was updated successfully.',
        ], 200);
    }
}