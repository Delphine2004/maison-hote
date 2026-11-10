<?php

namespace App\Repository;

use App\Entity\Room;
use App\DTO\SearchRoomDTO;
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


    public function findAllRooms(): array
    {
        return $this->findAll();
    }

    public function findRoomsByPeriod(
        ?SearchRoomDTO $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {

        $bookingStatus = BookingStatus::CONFIRMED;
        $startDate = $criteria->getStartingDate();
        $endDate = $criteria->getEndingDate();

        $start = (clone $startDate)->setTime(0, 0, 0);
        $end   = (clone $endDate)->setTime(23, 59, 59);


        // r = room - b = booking
        return $this->createQueryBuilder('r')
            ->select([
                'r.id',
                'r.number',
                'r.name',
                'r.description',
                'r.picture',
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
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Permet d'obtenir le nombre de chambre dispo par période
     *
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @return integer
     */
    public function countAvailableRoomsByPeriod(
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ): int {
        $startFormatted = $start->setTime(0, 0, 0);
        $endFormatted = $end->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            // On fait une jointure conditionnelle avec les réservations "OUTOFORDER" qui chevauchent la période
            ->leftJoin(
                'r.bookings',
                'b',
                'WITH',
                'b.startingDate <= :endDate AND b.endingDate >= :startDate AND b.status = :status'
            )
            // On ne garde que les chambres qui N'ONT PAS trouvé de réservation HS correspondante
            ->where('b.id IS NULL')
            ->setParameter('startDate', $startFormatted)
            ->setParameter('endDate', $endFormatted)
            ->setParameter('status', BookingStatus::OUTOFORDER->value)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
