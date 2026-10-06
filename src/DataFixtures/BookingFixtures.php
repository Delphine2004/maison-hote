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

            // Les arrivées
            [
                'number' => '1000',
                'startingDate' => $today,
                'endingDate'   => $today->modify('+5 days'),
                'totalAmount'  => 400.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number' => '1001',
                'startingDate' => $today,
                'endingDate'   => $today->modify('+5 days'),
                'totalAmount'  => 425.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number' => '1002',
                'startingDate' => $today,
                'endingDate'   => $today->modify('+3 days'),
                'totalAmount'  => 270.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            // Chambre HS
            [
                'number' => '1003',
                'startingDate' => $today,
                'endingDate'   => $today->modify('+2 days'),
                'totalAmount'  => 0.00, // en euros
                'status'       => BookingStatus::OUTOFORDER,
                'user'         => null,
                'room'         => $this->getReference('room_5', Room::class),
            ],

            // Les départs du jour
            [
                'number' => '1004',
                'startingDate' => $today->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 160.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number' => '1005',
                'startingDate' => $today->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 170.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number' => '1006',
                'startingDate' => $today->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 170.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],

            // Les clients présents
            [
                'number' => '1007',
                'startingDate' => $today->modify('-3 days'),
                'endingDate'   => $today->modify('+1 days'),
                'totalAmount'  => 380.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_5', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],

            // Les réservations en historique
            [
                'number' => '1',
                'startingDate' => $today->modify('-5 days'),
                'endingDate'   => $today->modify('-3 days'),
                'totalAmount'  => 180.00, // en euros
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number' => '2',
                'startingDate' => $today->modify('-5 days'),
                'endingDate'   => $today->modify('-2 days'),
                'totalAmount'  => 255.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],

            // Les réservations à venir
            [
                'number' => '1008',
                'startingDate' => $today->modify('+5 days'),
                'endingDate'   => $today->modify('+8 days'),
                'totalAmount'  => 240.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number' => '1009',
                'startingDate' => $today->modify('+6 days'),
                'endingDate'   => $today->modify('+10 days'),
                'totalAmount'  => 400.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number' => '1010',
                'startingDate' => $today->modify('+2 days'),
                'endingDate'   => $today->modify('+5 days'),
                'totalAmount'  => 300.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],

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

            $this->addReference('booking_' . $data['number'], $booking);
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
