<?php

namespace App\UseCase;

use App\DTO\RegisterDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class RegisterUser
{
    private $entityManager;
    private $passwordHasher;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function execute(RegisterDTO $dto): Response
    {

        $existingUser = $this->userRepository->findOneBy(['email' => $dto->email]);

        if ($existingUser) {
            return new Response('User email already exists', Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('User created successfully!', Response::HTTP_CREATED);
    }
}
