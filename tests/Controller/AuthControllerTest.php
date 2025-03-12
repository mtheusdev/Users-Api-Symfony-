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
    /** @var RegisterUserUseCase&\PHPUnit\Framework\MockObject\MockObject $registerUserUseCaseMock */
    private $registerUserUseCaseMock;

    /** @var LoginUserUseCase&\PHPUnit\Framework\MockObject\MockObject $loginUserUseCaseMock */
    private $loginUserUseCaseMock;

    private $controller;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validationService = self::getContainer()->get('App\Service\ValidationService');
        $this->registerUserUseCaseMock = $this->createMock(RegisterUserUseCase::class);
        $this->loginUserUseCaseMock = $this->createMock(LoginUserUseCase::class);
        $this->controller = new AuthController($this->validationService);
        $this->controller->setContainer(self::getContainer());
    }

    public function testRegisterUserSuccessfully(): void
    {

        $this->registerUserUseCaseMock
            ->method('execute')
            ->willReturn(new Response('User created successfully!', Response::HTTP_CREATED));


        $request = new Request([], [], [], [], [], [], json_encode([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ]));

        $response = $this->controller->register($request, $this->registerUserUseCaseMock);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('User created successfully!', $response->getContent());
    }

    public function testRegisterWithValidationErrors(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([
            'name' => '',
            'email' => '',
            'password' => ''
        ]));

        $response =  $this->controller->register($request, $this->registerUserUseCaseMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertNotEmpty($content);


        $expectedErrors = [
            ['field' => 'name', 'message' => 'Name is required.'],
            ['field' => 'email', 'message' => 'Email is required.'],
            ['field' => 'password', 'message' => 'Password is required.'],
            ['field' => 'password', 'message' => 'Password must have a minimum of 6 characters.']
        ];

        foreach ($expectedErrors as $index => $error) {
            $this->assertEquals($error, $content[$index]);
        }
    }

    public function testLoginWithValidationErrors(): void
    {

        $request = new Request([], [], [], [], [], [], json_encode([
            'name' => '',
            'password' => ''
        ]));

        $response =  $this->controller->login($request, $this->loginUserUseCaseMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                ["field" => "email", "message" => "Email is required."],
                ["field" => "password", "message" => "Password is required."]
            ]),
            $response->getContent()
        );
    }


    public function testLoginSuccessfully(): void
    {

        $this->loginUserUseCaseMock
            ->method('execute')
            ->willReturn(new Response('Login successful', Response::HTTP_OK));

        $request = new Request([], [], [], [], [], [], json_encode([
            'email' => 'email@email.com',
            'password' => 'password'
        ]));

        $response = $this->controller->login($request, $this->loginUserUseCaseMock);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Login successful', $response->getContent());
    }

    public function testLoginWithFailedAuthentication(): void
    {

        $this->loginUserUseCaseMock
            ->method('execute')
            ->willReturn(new Response('Invalid credentials', Response::HTTP_UNAUTHORIZED));

        $request = new Request([], [], [], [], [], [], json_encode([
            'email' => 'wrongemail@email.com',
            'password' => 'wrongpassword'
        ]));

        $response = $this->controller->login($request, $this->loginUserUseCaseMock);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals('Invalid credentials', $response->getContent());
    }
}
