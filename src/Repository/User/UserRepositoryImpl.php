<?php

namespace App\Repository\User;

use App\Entity\User;
use App\Repository\User\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepositoryImpl extends ServiceEntityRepository implements UserRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->em = $this->getEntityManager();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}
