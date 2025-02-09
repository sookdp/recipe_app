<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Liste de 5 utilisateurs
        $usersData = [
            ['email' => 'user1@example.com', 'password' => 'password123', 'roles' => ['ROLE_USER']],
            ['email' => 'user2@example.com', 'password' => 'password123', 'roles' => ['ROLE_USER']],
            ['email' => 'admin1@example.com', 'password' => 'adminpass', 'roles' => ['ROLE_ADMIN']],
            ['email' => 'chef1@example.com', 'password' => 'chefpass', 'roles' => ['ROLE_CHEF']],
            ['email' => 'guest1@example.com', 'password' => 'guestpass', 'roles' => ['ROLE_USER']],
            ['email' => 'user3@example.com', 'password' => 'password123', 'roles' => ['ROLE_USER']],
            ['email' => 'user4@example.com', 'password' => 'password123', 'roles' => ['ROLE_USER']],
            ['email' => 'admin2@example.com', 'password' => 'adminpass', 'roles' => ['ROLE_ADMIN']],
            ['email' => 'chef2@example.com', 'password' => 'chefpass', 'roles' => ['ROLE_CHEF']],
            ['email' => 'guest2@example.com', 'password' => 'guestpass', 'roles' => ['ROLE_USER']],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);

            // Hachage du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
