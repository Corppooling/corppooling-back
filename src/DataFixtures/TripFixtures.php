<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Trip;
use App\Utils\TripType;

class TripFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {


        for ($i = 0; $i < 20; $i++) {
            $trip = new Trip();
            $trip->setDepartureLocation("Lyon");
            $trip->setArrivalLocation("Paris");
            $trip->setAvailableSeats(4);
            $trip->setDepartureTime(new \DateTimeImmutable());
            $trip->setUpdatedAt(new \DateTimeImmutable());
            $trip->setCreatedAt(new \DateTimeImmutable());
            $trip->setMessage("Nouveau message");
            $trip->setPrice(4);
            $trip->setCarModel("Peugot 206");
            $trip->setCarColor("Rouge");
            $trip->setType(TripType::Driver->value);
            $trip->setAnnouncer($this->getReference(AppFixtures::USER_REFERENCE));
            $trip->setCompany($this->getReference(AppFixtures::COMPANY_REFERENCE));
            $manager->persist($trip);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class,
        ];
    }
}
