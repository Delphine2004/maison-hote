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
                'endingDate'   => (clone $today)->modify('+5 days'),
                'totalAmount'  => 425.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number' => '1002',
                'startingDate' => $today,
                'endingDate'   => (clone $today)->modify('+3 days'),
                'totalAmount'  => 270.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            // Chambre HS
            [
                'number' => '1003',
                'startingDate' => $today,
                'endingDate'   => (clone $today)->modify('+2 days'),
                'totalAmount'  => 0.00, // en euros
                'status'       => BookingStatus::OUTOFORDER,
                'user'         => null,
                'room'         => $this->getReference('room_5', Room::class),
            ],

            // Les départs du jour
            [
                'number' => '1004',
                'startingDate' => (clone $today)->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 160.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number' => '1005',
                'startingDate' => (clone $today)->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 170.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number' => '1006',
                'startingDate' => (clone $today)->modify('-2 days'),
                'endingDate'   => $today,
                'totalAmount'  => 170.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],

            // Les clients présents
            [
                'number' => '1007',
                'startingDate' => (clone $today)->modify('-3 days'),
                'endingDate'   => (clone $today)->modify('+1 days'),
                'totalAmount'  => 380.00, // en euros
                'status'       => BookingStatus::IN,
                'user'         => $this->getReference('client_5', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],


            // Les réservations en historique
            [
                'number'      => '1',
                'startingDate' => (clone $today)->modify('-12 days'),
                'endingDate'   => (clone $today)->modify('-10 days'),
                'totalAmount'  => 180.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'      => '2',
                'startingDate' => (clone $today)->modify('-10 days'),
                'endingDate'   => (clone $today)->modify('-7 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'      => '3',
                'startingDate' => (clone $today)->modify('-18 days'),
                'endingDate'   => (clone $today)->modify('-17 days'),
                'totalAmount'  => 85.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'      => '4',
                'startingDate' => (clone $today)->modify('-8 days'),
                'endingDate'   => (clone $today)->modify('-5 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'      => '5',
                'startingDate' => (clone $today)->modify('-15 days'),
                'endingDate'   => (clone $today)->modify('-12 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'      => '6',
                'startingDate' => (clone $today)->modify('-9 days'),
                'endingDate'   => (clone $today)->modify('-6 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'      => '7',
                'startingDate' => (clone $today)->modify('-20 days'),
                'endingDate'   => (clone $today)->modify('-17 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'      => '8',
                'startingDate' => (clone $today)->modify('-14 days'),
                'endingDate'   => (clone $today)->modify('-11 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'      => '9',
                'startingDate' => (clone $today)->modify('-25 days'),
                'endingDate'   => (clone $today)->modify('-22 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'      => '10',
                'startingDate' => (clone $today)->modify('-7 days'),
                'endingDate'   => (clone $today)->modify('-4 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'      => '11',
                'startingDate' => (clone $today)->modify('-6 days'),
                'endingDate'   => (clone $today)->modify('-3 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_7', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '12',
                'startingDate' => (clone $today)->modify('-30 days'),
                'endingDate'   => (clone $today)->modify('-27 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_9', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '13',
                'startingDate' => (clone $today)->modify('-28 days'),
                'endingDate'   => (clone $today)->modify('-25 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_8', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'       => '14',
                'startingDate' => (clone $today)->modify('-24 days'),
                'endingDate'   => (clone $today)->modify('-22 days'),
                'totalAmount'  => 170.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'       => '15',
                'startingDate' => (clone $today)->modify('-23 days'),
                'endingDate'   => (clone $today)->modify('-19 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '16',
                'startingDate' => (clone $today)->modify('-21 days'),
                'endingDate'   => (clone $today)->modify('-18 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'       => '17',
                'startingDate' => (clone $today)->modify('-19 days'),
                'endingDate'   => (clone $today)->modify('-16 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_10', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '18',
                'startingDate' => (clone $today)->modify('-17 days'),
                'endingDate'   => (clone $today)->modify('-15 days'),
                'totalAmount'  => 170.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'       => '19',
                'startingDate' => (clone $today)->modify('-16 days'),
                'endingDate'   => (clone $today)->modify('-12 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_9', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '20',
                'startingDate' => (clone $today)->modify('-13 days'),
                'endingDate'   => (clone $today)->modify('-11 days'),
                'totalAmount'  => 170.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'       => '21',
                'startingDate' => (clone $today)->modify('-12 days'),
                'endingDate'   => (clone $today)->modify('-9 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'       => '22',
                'startingDate' => (clone $today)->modify('-11 days'),
                'endingDate'   => (clone $today)->modify('-8 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'       => '23',
                'startingDate' => (clone $today)->modify('-9 days'),
                'endingDate'   => (clone $today)->modify('-6 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_7', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '24',
                'startingDate' => (clone $today)->modify('-8 days'),
                'endingDate'   => (clone $today)->modify('-5 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '25',
                'startingDate' => (clone $today)->modify('-7 days'),
                'endingDate'   => (clone $today)->modify('-4 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'       => '26',
                'startingDate' => (clone $today)->modify('-6 days'),
                'endingDate'   => (clone $today)->modify('-2 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::FINALIZED,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],

            // Les réservations à venir
            [
                'number' => '1008',
                'startingDate' => (clone $today)->modify('+5 days'),
                'endingDate'   => (clone $today)->modify('+8 days'),
                'totalAmount'  => 240.00, // en euros
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number' => '1009',
                'startingDate' => (clone $today)->modify('+6 days'),
                'endingDate'   => (clone $today)->modify('+10 days'),
                'totalAmount'  => 400.00, // en euros
                'status'       => BookingStatus::CANCELLED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '1010',
                'startingDate' => (clone $today)->modify('+4 days'),
                'endingDate'   => (clone $today)->modify('+7 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '1011',
                'startingDate' => (clone $today)->modify('+6 days'),
                'endingDate'   => (clone $today)->modify('+8 days'),
                'totalAmount'  => 170.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_4', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'       => '1012',
                'startingDate' => (clone $today)->modify('+8 days'),
                'endingDate'   => (clone $today)->modify('+12 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_5', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '1013',
                'startingDate' => (clone $today)->modify('+10 days'),
                'endingDate'   => (clone $today)->modify('+13 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'       => '1014',
                'startingDate' => (clone $today)->modify('+12 days'),
                'endingDate'   => (clone $today)->modify('+15 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_7', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'       => '1015',
                'startingDate' => (clone $today)->modify('+14 days'),
                'endingDate'   => (clone $today)->modify('+16 days'),
                'totalAmount'  => 170.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_8', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'       => '1016',
                'startingDate' => (clone $today)->modify('+16 days'),
                'endingDate'   => (clone $today)->modify('+20 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_9', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '1017',
                'startingDate' => (clone $today)->modify('+18 days'),
                'endingDate'   => (clone $today)->modify('+21 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_10', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'       => '1018',
                'startingDate' => (clone $today)->modify('+20 days'),
                'endingDate'   => (clone $today)->modify('+23 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_8', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '1019',
                'startingDate' => (clone $today)->modify('+22 days'),
                'endingDate'   => (clone $today)->modify('+25 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_1', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '1020',
                'startingDate' => (clone $today)->modify('+24 days'),
                'endingDate'   => (clone $today)->modify('+28 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_2', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'       => '1021',
                'startingDate' => (clone $today)->modify('+27 days'),
                'endingDate'   => (clone $today)->modify('+30 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_5', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'       => '1022',
                'startingDate' => (clone $today)->modify('+30 days'),
                'endingDate'   => (clone $today)->modify('+33 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '1023',
                'startingDate' => (clone $today)->modify('+32 days'),
                'endingDate'   => (clone $today)->modify('+35 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_8', User::class),
                'room'         => $this->getReference('room_1', Room::class),
            ],
            [
                'number'       => '1024',
                'startingDate' => (clone $today)->modify('+35 days'),
                'endingDate'   => (clone $today)->modify('+39 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_9', User::class),
                'room'         => $this->getReference('room_5', Room::class),
            ],
            [
                'number'       => '1025',
                'startingDate' => (clone $today)->modify('+38 days'),
                'endingDate'   => (clone $today)->modify('+41 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_10', User::class),
                'room'         => $this->getReference('room_3', Room::class),
            ],
            [
                'number'       => '1026',
                'startingDate' => (clone $today)->modify('+42 days'),
                'endingDate'   => (clone $today)->modify('+45 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_6', User::class),
                'room'         => $this->getReference('room_2', Room::class),
            ],
            [
                'number'       => '1027',
                'startingDate' => (clone $today)->modify('+45 days'),
                'endingDate'   => (clone $today)->modify('+49 days'),
                'totalAmount'  => 340.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_3', User::class),
                'room'         => $this->getReference('room_4', Room::class),
            ],
            [
                'number'       => '1028',
                'startingDate' => (clone $today)->modify('+50 days'),
                'endingDate'   => (clone $today)->modify('+53 days'),
                'totalAmount'  => 255.00,
                'status'       => BookingStatus::CONFIRMED,
                'user'         => $this->getReference('client_7', User::class),
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
