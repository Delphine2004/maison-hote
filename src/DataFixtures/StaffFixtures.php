<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StaffFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $staff = new User();
        $staff->setEmail('staff@guesthouse.fr');
        $staff->setLogin('front-office');
        $staff->setRoles([UserRole::EMPLOYE]);
        $hashedPassword = $this->hasher->hashPassword($staff, 'frontoffiche123');
        $staff->setPassword($hashedPassword, true);

        $manager->persist($staff);
        $manager->flush();
    }
}
