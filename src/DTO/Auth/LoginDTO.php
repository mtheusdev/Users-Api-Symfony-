<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDTO
{
    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email(message: "Invalid email.")]
    public ?string $email;

    #[Assert\NotBlank(message: "Password is required.")]
    public ?string $password;

    public function __construct(?string $email, ?string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}
