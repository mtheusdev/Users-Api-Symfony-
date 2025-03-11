<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterDTO
{
    #[Assert\NotBlank(message: "O nome é obrigatório.")]
    #[Assert\Length(max: 255, maxMessage: "O nome não pode ter mais de 255 caracteres.")]
    public ?string $name;

    #[Assert\NotBlank(message: "O e-mail é obrigatório.")]
    #[Assert\Email(message: "O e-mail fornecido não é válido.")]
    public ?string $email;

    #[Assert\NotBlank(message: "A senha é obrigatória.")]
    #[Assert\Length(min: 6, minMessage: "A senha deve ter pelo menos 6 caracteres.")]
    public ?string $password;

    public function __construct(?string $name = null, ?string $email = null, ?string $password = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}
