<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i < 10; $i++) {
            $client = new Client();
            $client->setFirstName($faker->firstName);
            $client->setLastName($faker->lastName);
            $client->setEmail($faker->email);
            $client->setBirthDate($faker->dateTime);

            $address = new Address();
            $address->setStreet($faker->buildingNumber . ' ' . $faker->streetName);
            $address->setPostalCode($faker->postcode);
            $address->setCity($faker->city);

            $client->addAddress($address);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
