<?php

namespace App\Tests\UseCase;

use App\DTO\Auth\RegisterDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use App\UseCase\Auth\RegisterUserUseCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserUseCaseTest extends TestCase
{

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&UserRepository
     */
    private $userRepository;


    private $registerUser;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->registerUser = new RegisterUserUseCase(
            $this->passwordHasher,
            $this->userRepository
        );
    }

    public function testRegisterUserWhenEmailExists(): void
    {
        $existingUser = new User();
        $existingUser->setEmail('test@example.com');
        $this->userRepository
            ->method('findOneByEmail')
            ->willReturn($existingUser);

        $dto = new RegisterDTO('John Doe', 'test@example.com', 'password123');

        $response = $this->registerUser->execute($dto);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals('User email already exists', $response->getContent());
    }

    public function testRegisterUserSuccessfully(): void
    {
        $this->userRepository
            ->method('findOneBy')
            ->willReturn(null);

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $dto = new RegisterDTO('John Doe', 'john@example.com', 'password123');

        $response = $this->registerUser->execute($dto);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('User created successfully!', $response->getContent());
    }
}
