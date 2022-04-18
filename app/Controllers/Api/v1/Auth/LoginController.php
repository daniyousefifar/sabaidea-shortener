<?php

namespace App\Controllers\Api\v1\Auth;

use App\Controllers\Controller;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Selective\Validation\Exception\ValidationException;
use Selective\Validation\Factory\CakeValidationFactory;
use Src\Actions\ActionError;
use Src\Settings\SettingsInterface;

class LoginController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        ContainerInterface $container,
        LoggerInterface $logger,
        UserRepositoryInterface $userRepository
    )
    {
        parent::__construct($container, $logger);
        $this->userRepository = $userRepository;
    }

    protected function action(): Response
    {
        $formData = $this->getFormData();

        $validationFactory = new CakeValidationFactory();
        $validator = $validationFactory->createValidator();

        $validator
            ->requirePresence('email', true, 'The email field is required.')
            ->notEmptyString('email', 'The email field is required.')
            ->email('email', false, 'The email must be a valid email address.')
            ->requirePresence('password', true, 'The password field is required.')
            ->notEmptyString('password', 'The password field is required.')
            ->minLength('password', 8, 'The password must be at least 8 characters.')
            ->maxLength('password', 20, 'The password must not be greater than 20 characters.');

        $validationResult = $validationFactory->createValidationResult(
            $validator->validate($formData)
        );

        if ($validationResult->fails()) {
            throw new ValidationException('Validation failed. Please check your input.', $validationResult);
        }

        try {
            $user = $this->userRepository->findUserByEmail($formData['email']);

            if (password_verify($formData['password'], $user['password'])) {
                $settings = $this->container->get(SettingsInterface::class);
                $jwtSettings = $settings->get('jwt');

                $payload = [
                    'uid' => $user['id'],
                    'iss' => "{$this->request->getUri()->getScheme()}://{$this->request->getUri()->getHost()}",
                    'iat' => time(),
                    'exp' => strtotime('+1 day', time()),
                ];

                $token = JWT::encode($payload, $jwtSettings['secret'], $jwtSettings['algorithm']);

                return $this->respondWithData([
                    'token' => $token,
                    'token_type' => 'Bearer'
                ], 200);
            } else {
                return $this->respondWithError('These credentials do not match our records.', ActionError::UNAUTHENTICATED, 401);
            }
        } catch (UserNotFoundException $e) {
            return $this->respondWithError('These credentials do not match our records.', ActionError::UNAUTHENTICATED, 401);
        } catch (\Exception $e) {
            return $this->respondWithError('Something went wrong. try again.', ActionError::SERVER_ERROR, 500);
        }
    }
}