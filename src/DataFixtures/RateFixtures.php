<?php

namespace App\DataFixtures;

use App\Entity\Rate;
use App\Entity\Room;
use App\Entity\Period;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class RateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        // 1 tarif par chambre et par période
        // chambre -> clé des périodes -> tarifs associés
        $rateData = [
            1 => [1 => 12000, 2 => 10000, 3 => 9000],
            2 => [1 => 15000, 2 => 13000, 3 => 11000],
            3 => [1 => 18000, 2 => 16000, 3 => 14000],
            4 => [1 => 17000, 2 => 15000, 3 => 13000],
            5 => [1 => 20000, 2 => 18000, 3 => 16000],
        ];


        // création des tarifs
        foreach ($rateData as $roomNumber => $periods) {
            foreach ($periods as $periodNumber => $amount) {

                $rate = new Rate();
                $rate->setAmount($amount);

                $room = $this->getReference('room_' . $roomNumber, Room::class);
                $period = $this->getReference('period_' . $periodNumber, Period::class);

                $rate->setRoom($room);
                $rate->setPeriod($period);

                $manager->persist($rate);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PeriodFixtures::class,
            RoomFixtures::class,
        ];
    }
}
