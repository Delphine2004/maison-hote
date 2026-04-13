<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Enum\BookingStatus;

use DateTimeImmutable;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findBookingsByFilters(
        ?array $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {

        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r');

        // Booking Id
        if (!empty($criteria['booking_id'])) {
            $qb->andWhere('b.id = :id')
                ->setParameter('id', $criteria['booking_id']);
        }

        // Nom client
        if (!empty($criteria['last_name'])) {
            $qb->andWhere('.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $criteria['last_name'] . '%');
        }

        // Booking Statut
        if (!empty($criteria['status'])) {
            $qb->andWhere('b.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        // Dates
        $fieldMap = [
            'starting_date' => 'startingDate',
            'ending_date'   => 'endingDate',
            'created_at'    => 'createdAt',
        ];
        foreach ($fieldMap as $key => $field) {
            if (!empty($criteria[$key])) {

                $date = $criteria[$key];

                if (!$date instanceof \DateTime) {
                    $date = new \DateTime($date);
                }

                $startOfDay = (clone $date)->setTime(0, 0, 0);
                $endOfDay   = (clone $date)->setTime(23, 59, 59);


                $qb->andWhere("b.$field BETWEEN :{$key}_start AND :{$key}_end")
                    ->setParameter("{$key}_start", $startOfDay)
                    ->setParameter("{$key}_end", $endOfDay);
            }
        }

        return $qb->orderBy('b.id',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingBookingsByClient(
        int $userId,
        DateTimeImmutable $day
    ) {
        $start = $day->setTime(0, 0, 0);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.user = :userId')
            ->andWhere('b.status = :status')
            ->andWhere('b.endingDate < :start')
            ->setParameter('userId', $userId)
            ->setParameter('status', BookingStatus::CONFIRMED->value)
            ->setParameter('start', $start)
            ->orderBy('b.startingDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastBookingsByClient(
        int $userId,
        DateTimeImmutable $day
    ) {
        $end = $day->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.user = :userId')
            ->andWhere('b.endingDate < :end')
            ->setParameter('userId', $userId)
            ->setParameter('end', $end)
            ->orderBy('b.startingDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findInHouse(): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.status = :status')
            ->setParameter('status', BookingStatus::IN->value)
            ->getQuery()
            ->getResult();
    }


    public function findCheckOutsForDay(
        DateTimeImmutable $day
    ): array {
        $start = $day->setTime(0, 0, 0);
        $end   = $day->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.status = :status')
            ->andWhere('b.endingDate BETWEEN :start AND :end')
            ->setParameter('status', BookingStatus::IN->value)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findCheckInsForDay(
        DateTimeImmutable $day
    ): array {
        $start = $day->setTime(0, 0, 0);
        $end   = $day->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.status = :status')
            ->andWhere('b.startingDate BETWEEN :start AND :end')
            ->setParameter('status', BookingStatus::CONFIRMED->value)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }



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
