<?php

namespace App\UseCase\Auth;

use App\DTO\Auth\RegisterDTO;
use App\Entity\User;
use App\Repository\User\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserUseCase
{
    private $passwordHasher;
    private $userRepository;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function execute(RegisterDTO $dto): Response
    {

        $existingUser = $this->userRepository->findOneByEmail($dto->email);

        if ($existingUser) {
            return new Response('User email already exists', Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->userRepository->save($user);

        return new Response('User created successfully!', Response::HTTP_CREATED);
    }
}
