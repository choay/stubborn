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
        $user = new User();
        $user->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
            ->setApiToken('admin_token')
        ;

        $manager->persist($user);
        $manager->flush();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail('user'.$i.'@example.com')
                ->setRoles(['ROLE_USER'])
                ->setVerified(true)
                ->setPassword($this->passwordHasher->hashPassword($user, '000000'))
                ->setApiToken('user'.$i.'_token');

            $manager->persist($user);
            $manager->flush();
        }


    }
}
