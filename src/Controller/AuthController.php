<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\DTO\RegisterDTO;
use App\Service\ValidationService;

#[Route('/auth')]
class AuthController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $userRepository;
    private $validationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        ValidationService $validationService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->validationService = $validationService;
    }

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);


        $dto = new RegisterDTO(
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? ''
        );

        $errors = $this->validationService->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $this->userRepository->findOneBy(['email' => $dto->email]);

        if ($existingUser) {
            return new Response('Email já está em uso', Response::HTTP_CONFLICT);
        }


        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user,  $dto->password));
        $user->setCreatedAt(new \DateTimeImmutable());




        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('Usuário registrado com sucesso', Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new Response('Preencha todos os campos', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new Response('Usuário não encontrado', Response::HTTP_NOT_FOUND);
        }


        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return new Response('Senha incorreta', Response::HTTP_UNAUTHORIZED);
        }

        return new Response('Login realizado com sucesso');
    }
}
