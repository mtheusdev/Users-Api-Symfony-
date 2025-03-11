<?php

namespace App\UseCase;

use App\DTO\LoginDTO;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginUser
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
        $user = $this->userRepository->findOneBy(['email' => $dto->email]);
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $dto->password)) {
            return new Response('Invalid email or password', Response::HTTP_UNAUTHORIZED);
        }

        return new Response('User logged in successfully', Response::HTTP_OK);
    }
}
