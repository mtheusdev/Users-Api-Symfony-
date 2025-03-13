<?php

namespace App\UseCase\Auth;

use App\DTO\Auth\LoginDTO;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginUserUseCase
{
    private $userRepository;
    private $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function execute(LoginDTO $dto): Response
    {
        $user = $this->userRepository->findOneByEmail($dto->email);
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $dto->password)) {
            return new Response('Invalid email or password', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ]
        ], Response::HTTP_OK);
    }
}
