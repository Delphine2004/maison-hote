<?php

namespace App\Repository;

use App\Entity\Room;
use App\Enum\BookingStatus;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }


    public function findAllRoom(): array
    {
        return $this->findAll();
    }

    public function findRoomsByPeriod(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
    ): array {

        $bookingStatus = BookingStatus::CONFIRMED;

        // r = room - b = booking
        return $this->createQueryBuilder('r')
            ->select([
                'r.id AS room_id',
                'r.number',
                'r.name',
                'r.rate',
                'DATE_DIFF(:dateEnd, :dateStart) AS nights',
                '(DATE_DIFF(:dateEnd, :dateStart) * r.rate) AS total_price'
            ])
            ->leftJoin(
                'r.bookings',
                'b',
                'WITH',
                'b.startingDate < :dateEnd 
                AND b.endingDate > :dateStart 
                AND b.status = :statusBooking'
            )
            ->where('b.id IS NULL')
            ->setParameter('dateStart', $start)
            ->setParameter('dateEnd', $end)
            ->setParameter('statusBooking', $bookingStatus->value)

            ->getQuery()
            ->getResult();
    }
}
