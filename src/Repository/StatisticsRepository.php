<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\User;
use App\Entity\Room;
use App\DTO\SearchBookingDTO;
use App\Enum\BookingStatus;

use DateTimeImmutable;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class StatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    // TO = (total chambres occupées / total chambre dispo) x 100

    // Le Prix Moyen (ou ARR / ADR) = CA / nombre de chambres occupées

    // Le RevPAR (Revenue Per Available Room) = ca / total chambre dispo

    // La Durée Moyenne de Séjour (DMS) = nb de nuitées / nb total de réservation

    public function sumTotalAmountByCreationPeriod(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        BookingStatus $status
    ): float {
        $startFormatted =  $start->setTime(0, 0, 0);
        $endFormatted = $end->setTime(23, 59, 59);

        return (float) $this->createQueryBuilder('b')
            ->select('SUM(b.totalAmount)')
            ->andWhere('b.createdAt BETWEEN :start AND :end')
            ->andWhere('b.status = :status')
            ->setParameter('start', $startFormatted)
            ->setParameter('end', $endFormatted)
            ->setParameter('status', $status->value)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
