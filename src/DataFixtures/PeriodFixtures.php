<?php

namespace App\DataFixtures;

use App\Entity\Period;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PeriodFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $date = new \DateTimeImmutable('today');
        $start = $date->setTime(0, 0, 0);

        $periodData = [
            [
                'number' => 1,
                'name' => 'Basse Saison',
                'start' => $start,
                'end'   => $start->modify('+4 months'),
            ],
            [
                'number' => 2,
                'name' => 'Moyenne Saison',
                'start' => $start->modify('+4 months'),
                'end'   => $start->modify('+8 months'),
            ],
            [
                'number' => 3,
                'name' => 'Haute Saison',
                'start' => $start->modify('+8 months'),
                'end'   => $start->modify('+12 months'),
            ],
        ];

        foreach ($periodData as $data) {
            $period = new Period();
            $period->setName($data['name']);
            $period->setStartingDate($data['start']);
            $period->setEndingDate($data['end']);

            $manager->persist($period);

            $this->addReference('period_' . $data['number'], $period);
        }

        $manager->flush();
    }
}
