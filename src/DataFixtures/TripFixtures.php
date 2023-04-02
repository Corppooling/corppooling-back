<?php

namespace App\DataFixtures;

use App\DoctrineType\TripMissing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trip;
use DateTimeImmutable;
use Faker\Factory;
use Faker\Provider\Fakecar;

class TripFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker));
        for ($i = 0; $i < 20; $i++) {
            $trip = new Trip();
            $trip->setDepartureLocation($faker->city());
            $trip->setArrivalLocation($faker->city());
            $trip->setAvailableSeats($faker->numberBetween(1, 4));
            $trip->setDepartureTime($faker->dateTime());
            $trip->setUpdatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
            $trip->setCreatedAt(new DateTimeImmutable($faker->dateTimeBetween()->format('Y-m-d H:i:s')));
            $trip->setMessage($faker->text(200));
            $trip->setPrice($faker->numberBetween(0, 25));
            $trip->setCarModel($faker->vehicle());
            $trip->setCarColor($faker->colorName());
            $trip->setType($faker->boolean() ? TripMissing::Driver : TripMissing::Passenger);
            $trip->setAnnouncer($this->getReference(AppFixtures::USER_REFERENCE));
            $trip->setCompany($this->getReference($faker->boolean() ? AppFixtures::COMPANY_FIRST_REFERENCE : AppFixtures::COMPANY_LAST_REFERENCE));
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
