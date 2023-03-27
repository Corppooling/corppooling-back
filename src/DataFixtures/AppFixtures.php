<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Department;
use App\Entity\Company;
use App\Entity\Cluster;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $company = new Company();
        $company->setName("Hello CSE");
        $company->setSiren("829190248");
        $company->setLogo("https://startup.info/wp-content/uploads/2020/10/logo-hellocse-original.png");
        $company->setAuthCode("1234569");
        $company->setUpdatedAt(new \DateTimeImmutable());
        $company->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($company);

        $company2 = new Company();
        $company2->setName("Indeeed");
        $company2->setSiren("829190229");
        $company2->setLogo("https://upload.wikimedia.org/wikipedia/commons/f/fa/Indeed_logo.png");
        $company2->setAuthCode("12345609");
        $company2->setUpdatedAt(new \DateTimeImmutable());
        $company2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($company2);

        $departments = [
            'Direction générale',
            'Ressources humaines',
            'Finances',
            'Ventes',
            'Marketing'
        ];

        $departments_entities = [];

        foreach ($departments as $departmentName) {
            $department = new Department();
            $department->setName($departmentName);
            $department->setUpdatedAt(new \DateTimeImmutable());
            $department->setCreatedAt(new \DateTimeImmutable());
            $departments_entities[] = $department;
            $manager->persist($department);
        }

        $cluster = new Cluster();
        $cluster->setName("Cluster numéro 1");
        $cluster->setAuthCode("908972");
        $cluster->addCompany($company);
        $cluster->addCompany($company2);
        $manager->persist($cluster);


        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setFirstname('Jean');
        $user1->setLastname('Michel');
        $user1->setPassword($this->passwordEncoder->hashPassword($user1, 'password1'));
        $user1->setCompany($company);
        $user1->setDepartment($departments_entities[1]);
        $user1->setUpdatedAt(new \DateTimeImmutable());
        $user1->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setFirstname('Jane');
        $user2->setLastname('Pierre');
        $user2->setPassword($this->passwordEncoder->hashPassword($user2, 'password2'));
        $user2->setCompany($company);
        $user1->setDepartment($departments_entities[2]);
        $user2->setUpdatedAt(new \DateTimeImmutable());
        $user2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user2);

        $manager->flush();
    }
}
