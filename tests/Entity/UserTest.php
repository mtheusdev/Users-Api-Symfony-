<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testInitialValues(): void
    {
        $user = new User();

        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertNotNull($user->getCreatedAt());
        $this->assertNull($user->getId());
        $this->assertNull($user->getName());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getPassword());
    }

    public function testSettersAndGetters(): void
    {
        $user = new User();

        $user->setName('John Doe');
        $this->assertEquals('John Doe', $user->getName());

        $user->setEmail('johndoe@example.com');
        $this->assertEquals('johndoe@example.com', $user->getEmail());

        $user->setPassword('password');
        $this->assertEquals('password', $user->getPassword());
    }

    public function testConstructorSetsCreatedAt(): void
    {
        $user = new User();

        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $user = new User();

        $date = new \DateTimeImmutable('2023-03-12 10:00:00');
        $user->setCreatedAt($date);

        $this->assertEquals($date, $user->getCreatedAt());
    }

    public function testGettersAndSettersWithEmptyValues(): void
    {
        $user = new User();

        $user->setName('');
        $this->assertEquals('', $user->getName());

        $user->setEmail('');
        $this->assertEquals('', $user->getEmail());

        $user->setPassword('');
        $this->assertEquals('', $user->getPassword());
    }

    public function testGetUsername(): void
    {
        $user = new User();
        $user->setEmail('johndoe@example.com');

        $this->assertEquals('johndoe@example.com', $user->getUsername());
    }

    public function testGetRoles(): void
    {
        $user = new User();

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetSalt(): void
    {
        $user = new User();

        $this->assertNull($user->getSalt());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->eraseCredentials();


        $this->assertTrue(true);
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('johndoe@example.com');

        $this->assertEquals('johndoe@example.com', $user->getUserIdentifier());
    }
}
