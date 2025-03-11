<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDTO
{
    #[Assert\NotBlank(message: "O e-mail é obrigatório.")]
    #[Assert\Email(message: "O e-mail fornecido não é válido.")]
    public ?string $email;

    #[Assert\NotBlank(message: "A senha é obrigatória.")]
    public ?string $password;

    public function __construct(?string $email = null, ?string $password = null)
    {
        $this->email = $email;
        $this->password = $password;
    }
}
