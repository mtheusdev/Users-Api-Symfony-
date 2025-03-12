<?php

namespace App\Repository\User;

use App\Entity\User;

interface UserRepository
{
    public function findOneByEmail(string $email): ?User;
    public function save(User $user): void;
}
