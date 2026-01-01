<?php

namespace App\Repository;

use App\Entity\Room;
use App\Enum\BookingStatus;
use App\Enum\RoomStatus;
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
        int $limit = 10,
        string $orderBy = 'ASC'
    ): array {

        $bookingStatus = BookingStatus::CONFIRMED;
        $roomStatus = RoomStatus::AVAILABLE;

        // r = room - p = period - b = booking - ra = rate
        return $this->createQueryBuilder('r')
            ->select([
                'r.id AS room_id',
                'r.number',
                'r.name',
                'r.status',
                'ra.amountCents',
                'DATE_DIFF(:dateEnd, :dateStart) AS nights',
                '(DATE_DIFF(:dateEnd, :dateStart) * ra.amountCents) AS total_price'
            ])
            ->leftJoin(
                'r.bookings',
                'b',
                'WITH',
                'b.startingDate < :dateEnd AND b.endingDate > :dateStart AND b.status = :statusBooking'
            )
            ->innerJoin('r.rates', 'ra')
            ->innerJoin(
                'ra.period',
                'p',
                'WITH',
                'p.startingDate <= :dateStart AND p.endingDate >= :dateEnd'
            )
            ->where('r.status = :statusRoom')
            ->andWhere('b.id IS NULL')
            ->setParameter('dateStart', $start)
            ->setParameter('dateEnd', $end)
            ->setParameter('statusBooking', $bookingStatus->value)
            ->setParameter('statusRoom', $roomStatus->value)

            ->orderBy('r.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
