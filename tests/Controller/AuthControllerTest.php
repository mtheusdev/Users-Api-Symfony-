<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\AuthController;
use App\UseCase\Auth\LoginUserUseCase;
use App\UseCase\Auth\RegisterUserUseCase;

class AuthControllerTest extends KernelTestCase
{
    private $validationService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validationService = self::getContainer()->get('App\Service\ValidationService');
    }

    public function testRegisterUserSuccessfully(): void
    {
        /** @var RegisterUserUseCase&\PHPUnit\Framework\MockObject\MockObject $registerUserUseCaseMock */
        $registerUserUseCaseMock = $this->createMock(RegisterUserUseCase::class);

        $registerUserUseCaseMock
            ->method('execute')
            ->willReturn(new Response('User created successfully!', Response::HTTP_CREATED));

        $controller = new AuthController($this->validationService);
        $controller->setContainer(self::getContainer());

        $request = new Request([], [], [], [], [], [], json_encode([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ]));

        $response = $controller->register($request, $registerUserUseCaseMock);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('User created successfully!', $response->getContent());
    }

    public function testRegisterWithValidationErrors(): void
    {
        /** @var RegisterUserUseCase&\PHPUnit\Framework\MockObject\MockObject $registerUserUseCaseMock */
        $registerUserUseCaseMock = $this->createMock(RegisterUserUseCase::class);


        $controller = new AuthController($this->validationService);
        $controller->setContainer(self::getContainer());

        $request = new Request([], [], [], [], [], [], json_encode([
            'name' => '',
            'email' => '',
            'password' => ''
        ]));

        $response = $controller->register($request, $registerUserUseCaseMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertNotEmpty($content);

        var_dump($content[0]['message']);

        $this->assertEquals('Name is required.', $content[0]['message']);
        $this->assertEquals('Email is required.', $content[1]['message']);
        $this->assertEquals('Password is required.', $content[2]['message']);
        $this->assertEquals('Password must have a minimum of 6 characters.', $content[3]['message']);
    }

    // public function testLoginWithValidationErrors(): void
    // {
    //     /** @var LoginUserUseCase&\PHPUnit\Framework\MockObject\MockObject $loginUserUseCaseMock */
    //     $loginUserUseCaseMock = $this->createMock(LoginUserUseCase::class);
    //     $loginUserUseCaseMock
    //         ->method('execute')
    //         ->willReturn(new Response('Login failed', Response::HTTP_UNAUTHORIZED));

    //     // Criando uma solicitação com dados de login inválidos
    //     $request = new Request([], ['email' => '', 'password' => '']); // Dados inválidos para validação

    //     // Instanciando o controlador com o serviço de validação
    //     $controller = new AuthController($this->validationService);

    //     // Chamando o método de login do controlador
    //     $response = $controller->login($request, $loginUserUseCaseMock);

    //     // Verificando se o status de erro (400) é retornado e as mensagens de erro
    //     $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    //     $this->assertJsonStringEqualsJsonString(
    //         json_encode(['Email is required', 'Password is required']),
    //         $response->getContent()
    //     );
    // }


    // public function testLoginSuccessfully(): void
    // {
    //     /** @var LoginUserUseCase&\PHPUnit\Framework\MockObject\MockObject $loginUserUseCaseMock */
    //     $loginUserUseCaseMock = $this->createMock(LoginUserUseCase::class);
    //     $loginUserUseCaseMock
    //         ->method('execute')
    //         ->willReturn(new Response('Login successful', Response::HTTP_OK));

    //     // Criando uma solicitação com dados de login válidos
    //     $request = new Request([], ['email' => 'test@example.com', 'password' => 'password123']);

    //     // Instanciando o controlador com o serviço de validação
    //     $controller = new AuthController($this->validationService);

    //     // Chamando o método de login do controlador
    //     $response = $controller->login($request, $loginUserUseCaseMock);

    //     // Verificando se o login foi bem-sucedido
    //     $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    //     $this->assertEquals('Login successful', $response->getContent());
    // }

    // public function testLoginWithFailedAuthentication(): void
    // {
    //     /** @var LoginUserUseCase&\PHPUnit\Framework\MockObject\MockObject $loginUserUseCaseMock */
    //     $loginUserUseCaseMock = $this->createMock(LoginUserUseCase::class);
    //     $loginUserUseCaseMock
    //         ->method('execute')
    //         ->willReturn(new Response('Invalid credentials', Response::HTTP_UNAUTHORIZED));

    //     // Criando uma solicitação com dados inválidos de login
    //     $request = new Request([], ['email' => 'wrong@example.com', 'password' => 'wrongpassword']);

    //     // Instanciando o controlador com o serviço de validação
    //     $controller = new AuthController($this->validationService);

    //     // Chamando o método de login do controlador
    //     $response = $controller->login($request, $loginUserUseCaseMock);

    //     // Verificando se o status de erro (401) é retornado
    //     $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    //     $this->assertEquals('Invalid credentials', $response->getContent());
    // }
}
