<?php

namespace App\Repository\User;

use App\Entity\User;

class UserRepositoryTestImpl implements UserRepository
{
    private array $users = [];

    public function findOneByEmail(string $email): ?User
    {
        return $this->users[$email] ?? null;
    }

    public function save(User $user): void
    {
        $this->users[$user->getEmail()] = $user;
    }
}
