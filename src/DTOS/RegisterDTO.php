<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterDTO
{
    #[Assert\NotBlank(message: "O nome é obrigatório.")]
    public ?string $name;

    #[Assert\NotBlank(message: "O email é obrigatório.")]
    #[Assert\Email(message: "O email informado não é válido.")]
    public ?string $email;

    #[Assert\NotBlank(message: "A senha é obrigatória.")]
    #[Assert\Length(min: 6, minMessage: "A senha deve ter pelo menos 6 caracteres.")]
    public ?string $password;

    public function __construct(?string $name, ?string $email, ?string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
