<?php

namespace App\Tests\UseCase;

use App\DTO\Auth\RegisterDTO;
use App\Entity\User;
use App\Repository\User\UserRepositoryTestImpl;
use App\UseCase\Auth\RegisterUserUseCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserUseCaseTest extends TestCase
{

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&UserPasswordHasherInterface
     */
    private $passwordHasher;

    private $userRepository;
    private $registerUser;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->userRepository = new UserRepositoryTestImpl();

        $this->registerUser = new RegisterUserUseCase(
            $this->passwordHasher,
            $this->userRepository
        );
    }

    public function testRegisterUserWhenEmailExists(): void
    {
        $existingUser = new User();
        $existingUser->setEmail('test@example.com');
        $this->userRepository->save($existingUser);

        $dto = new RegisterDTO('John Doe', 'test@example.com', 'password123');

        $response = $this->registerUser->execute($dto);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals('User email already exists', $response->getContent());
    }

    public function testRegisterUserSuccessfully(): void
    {

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $dto = new RegisterDTO('John Doe', 'john@example.com', 'password123');

        $response = $this->registerUser->execute($dto);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('User created successfully!', $response->getContent());
    }
}
