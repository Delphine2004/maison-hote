<?php

namespace App\DataFixtures;

use App\Entity\user;
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
        $user = new User();
        $user->setFirstName('Bruce');
        $user->setLastName('WAYNE');
        $user->setEmail('batman@batman.fr');
        $user->setPhone('0601020304');
        $user->setAddress('12 rue du manoir');
        $user->setCity('GOTHAM');
        $user->setZipCode('88000');

        $user->setRoles([UserRole::CLIENT]);
        $hashedPassword = $this->hasher->hashPassword($user, 'Azertyuiop12*');
        $user->setPassword($hashedPassword, true);

        $manager->persist($user);
        $manager->flush();
    }
}
