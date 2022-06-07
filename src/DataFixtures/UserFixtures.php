<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public const USER = [
        ['nickname' => 'admin', 'email' => 'admin@admin.com', 'password' => 'admin', 'role' => ['ROLE_ADMIN']],
        ['nickname' => 'contributor', 'email' => 'user@user.com', 'password' => 'user', 'role' => ['ROLE_CONTRIBUTOR']],
    ];

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        foreach (self::USER as  $myUser) {
            $user = new User();
            $user->setNickname($myUser['nickname']);
            $user->setEmail($myUser['email']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $myUser['password']
            );
            $user->setPassword($hashedPassword);
            $user->setRoles($myUser['role']);
            $manager->persist($user);
        }


        $manager->flush();
    }
}
