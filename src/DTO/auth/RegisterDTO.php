<?php

namespace App\DTO\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterDTO
{
    #[Assert\NotBlank(message: "Name is required.")]
    #[Assert\Length(max: 255, maxMessage: "Name must have a maximum of 255 characters.")]
    public ?string $name;

    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email(message: "Invalid email.")]
    public ?string $email;

    #[Assert\NotBlank(message: "Password is required.")]
    #[Assert\Length(min: 6, minMessage: "Password must have a minimum of 6 characters.")]
    public ?string $password;

    public function __construct(?string $name, ?string $email, ?string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
