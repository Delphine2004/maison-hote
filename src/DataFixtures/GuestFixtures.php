<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GuestFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $clientData = [

            [
                'number' => '1',
                'lastName' => 'WAYNE',
                'firstName' => 'Bruce',
                'email' => 'batman@batman.com',
                'phone' => '0601020304',
                'address' => '12 rue du manoir',
                'city' => 'GOTHAM',
                'zipCode' => '88000'
            ],
            [
                'number' => '2',
                'lastName' => 'KENT',
                'firstName' => 'Clark',
                'email' => 'superman@dailyplanet.com',
                'phone' => '0612345678',
                'address' => '344 Clinton Street',
                'city' => 'METROPOLIS',
                'zipCode' => '75000'
            ],
            [
                'number' => '3',
                'lastName' => 'STARK',
                'firstName' => 'Tony',
                'email' => 'ironman@starkindustries.com',
                'phone' => '0698765432',
                'address' => '10880 Malibu Point',
                'city' => 'MALIBU',
                'zipCode' => '90265'
            ],
            [
                'number' => '4',
                'lastName' => 'PARKER',
                'firstName' => 'Peter',
                'email' => 'spiderman@bugle.com',
                'phone' => '0623456789',
                'address' => '20 Ingram Street',
                'city' => 'QUEENS',
                'zipCode' => '11375'
            ],
            [
                'number' => '5',
                'lastName' => 'GRANGER',
                'firstName' => 'Hermione',
                'email' => 'hermione@poudlard.com',
                'phone' => '0634567890',
                'address' => '7 allée des Moldus',
                'city' => 'LONDON',
                'zipCode' => '12345'
            ],
            [
                'number' => '6',
                'lastName' => 'BOND',
                'firstName' => 'James',
                'email' => 'bond007@mi6.co.uk',
                'phone' => '0645678901',
                'address' => '30 Wellington Square',
                'city' => 'LONDON',
                'zipCode' => '13000'
            ]
        ];

        foreach ($clientData as $data) {
            $client = new User();
            $client->setFirstName($data['firstName']);
            $client->setLastName($data['lastName']);
            $client->setEmail($data['email']);
            $client->setPhone($data['phone']);
            $client->setAddress($data['address']);
            $client->setCity($data['city']);
            $client->setZipCode($data['zipCode']);
            $client->setRoles([UserRole::CLIENT]);
            $hashedPassword = $this->hasher->hashPassword($client, 'Azertyuiop12*');
            $client->setPassword($hashedPassword, true);

            $manager->persist($client);

            $this->addReference('client_' . $data['number'], $client);
        }
        $manager->flush();
    }
}
