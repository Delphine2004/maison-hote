<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use App\Enum\BookingStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $today = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));

        $bookingData = [
            // Faire des réservations passées, en cours, en départ, en arrivée et à venir
            // Modifier les dates, les prix et les réferences users (de 1 à 6)

            [
                'startingDate' => $today,
                'endingDate'   => $today->modify('+5 days'),
                'totalAmount'  => 500.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'startingDate' => $today->modify('-5 days'),
                'endingDate'   => $today->modify('-3 days'),
                'totalAmount'  => 500.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'startingDate' => $today,
                'endingDate'   => $today->modify('+3 days'),
                'totalAmount'  => 300.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'startingDate' => $today->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 450.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'startingDate' => $today->modify('-3 days'),
                'endingDate'   => $today->modify('+1 days'),
                'totalAmount'  => 450.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'startingDate' => $today->modify('+2 days'),
                'endingDate'   => $today->modify('+5 days'),
                'totalAmount'  => 450.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_5', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ]
        ];

        foreach ($bookingData as $data) {
            $booking = new Booking();
            $booking->setStartingDate($data['startingDate']);
            $booking->setEndingDate($data['endingDate']);
            $booking->setTotalAmount($data['totalAmount']);
            $booking->setStatus($data['status']);
            $booking->setUser($data['user']);
            $booking->setRoom($data['room']);

            $manager->persist($booking);
        }
        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            GuestFixtures::class,
            RoomFixtures::class
        ];
    }
}
