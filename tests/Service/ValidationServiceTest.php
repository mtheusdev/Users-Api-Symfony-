<?php

namespace App\Tests\Service;

use App\Service\ValidationService;
use App\DTO\Auth\LoginDTO;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class ValidationServiceTest extends TestCase
{
    private ValidationService $validationService;
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
        $this->validationService = new ValidationService($this->validator);
    }


    public function testValidateValidDto(): void
    {
        $loginDto = new LoginDTO('test@example.com', 'validpassword');

        $errors = $this->validationService->validate($loginDto);

        $this->assertEmpty($errors);
    }


    public function testValidateInvalidDto(): void
    {
        $loginDto = new LoginDTO('', '');

        $errors = $this->validationService->validate($loginDto);

        $this->assertNotEmpty($errors);
        $this->assertCount(2, $errors);
        $this->assertEquals('email', $errors[0]['field']);
        $this->assertEquals('Email is required.', $errors[0]['message']);
        $this->assertEquals('password', $errors[1]['field']);
        $this->assertEquals('Password is required.', $errors[1]['message']);
    }


    public function testValidateInvalidEmail(): void
    {
        $loginDto = new LoginDTO('invalid-email', 'validpassword');

        $errors = $this->validationService->validate($loginDto);

        $this->assertNotEmpty($errors);
        $this->assertCount(1, $errors);
        $this->assertEquals('email', $errors[0]['field']);
        $this->assertEquals('Invalid email.', $errors[0]['message']);
    }
}
