<?php

namespace App\DataFixtures;

use App\Entity\Client;
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
        $client = new Client();
        $client->setFirstName('Bruce');
        $client->setLastName('WAYNE');
        $client->setEmail('batman@batman.fr');
        $client->setPhone('0601020304');
        $client->setAddress('12 rue du manoir');
        $client->setCity('GOTHAM');
        $client->setZipCode('88000');

        $client->setRoles([UserRole::CLIENT]);
        $hashedPassword = $this->hasher->hashPassword($client, 'Azertyuiop12*');
        $client->setPassword($hashedPassword, true);

        $manager->persist($client);
        $manager->flush();
    }
}
