<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setFirstname('Jean');
        $user1->setLastname('Michel');
        $user1->setPassword($this->passwordEncoder->hashPassword($user1, 'password1'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setFirstname('Jane');
        $user2->setLastname('Pierre');
        $user2->setPassword($this->passwordEncoder->hashPassword($user2, 'password2'));
        $manager->persist($user2);

        $manager->flush();
    }
}
