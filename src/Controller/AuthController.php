<?php

namespace App\Controller;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Service\ValidationService;
use App\UseCase\Auth\LoginUserUseCase;
use App\UseCase\Auth\RegisterUserUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth')]
class AuthController extends AbstractController
{
    private $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, RegisterUserUseCase $registerUser): Response
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

        return $registerUser->execute($dto);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, LoginUserUseCase $loginUser): Response
    {
        $data = json_decode($request->getContent(), true);

        $dto = new LoginDTO(
            $data['email'] ?? null,
            $data['password'] ?? null
        );

        $errors = $this->validationService->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return $loginUser->execute($dto);
    }
}
