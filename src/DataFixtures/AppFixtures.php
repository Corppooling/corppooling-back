<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Department;
use App\Entity\Company;
use App\Entity\Cluster;
use DateTimeImmutable;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public const USER_REFERENCE = 'lambda-user';
    public const COMPANY_FIRST_REFERENCE = 'company-first';
    public const COMPANY_LAST_REFERENCE = 'company-last';

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $companies = [];

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $company = new Company();
            $company->setName($faker->company());
            $company->setSiren($faker->siren());
            $company->setLogo("https://picsum.photos/640/480");
            $company->setAuthCode($faker->randomNumber(5));
            $company->setUpdatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
            $company->setCreatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
            $companies[] = $company;
            $manager->persist($company);
        }
        $this->addReference(self::COMPANY_FIRST_REFERENCE, $companies[0]);
        $this->addReference(self::COMPANY_LAST_REFERENCE, $companies[9]);

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
            $department->setUpdatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
            $department->setCreatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
            $department->setCompany($companies[0]);
            $departments_entities[] = $department;
            $manager->persist($department);
        }


        for ($i = 0; $i < 3; $i++) {
            $cluster = new Cluster();
            $cluster->setName("CLSTR " . $faker->company());
            $cluster->setAuthCode($faker->randomNumber(5));
            for ($i = 0; $i < $faker->numberBetween(1, 3); $i++) {
                $company =
                    $cluster->addCompany($companies[$faker->numberBetween(0, 9)]);
            }
            $manager->persist($cluster);
        }


        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setFirstname('Jean');
        $user1->setLastname('Michel');
        $user1->setPassword($this->passwordEncoder->hashPassword($user1, 'password1'));
        $user1->setCompany($companies[$faker->numberBetween(0, 9)]);
        $user1->setDepartment($departments_entities[1]);
        $user1->setUpdatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
        $user1->setCreatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
        $user1->setPhone($faker->phoneNumber());
        $manager->persist($user1);

        $this->addReference(self::USER_REFERENCE, $user1);


        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setFirstname('Jane');
        $user2->setLastname('Pierre');
        $user2->setPassword($this->passwordEncoder->hashPassword($user2, 'password2'));
        $user2->setCompany($companies[$faker->numberBetween(0, 9)]);
        $user2->setDepartment($departments_entities[2]);
        $user2->setUpdatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
        $user2->setCreatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
        $user2->setPhone($faker->phoneNumber());
        $user2->setRoles(['ROLE_USER', 'ROLE_MANAGER']);
        $manager->persist($user2);
        $manager->flush();
    }
}
