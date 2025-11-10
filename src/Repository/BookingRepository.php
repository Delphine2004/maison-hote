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


    public function findBookingById(int $id): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'c')->addSelect('c')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    public function findBookingsByArrivalDate(DateTimeImmutable $startingDate, int $limit = 10, string $orderBy = 'ASC'): array
    {
        $startOfDay = (clone $startingDate)->setTime(0, 0, 0);
        $endOfDay = (clone $startingDate)->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'c')->addSelect('c')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.starting_date >= :startOfDay')
            ->andWhere('b.starting_date <= :endOfDay')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBookingsByDepartureDate(DateTimeImmutable $endingDate, int $limit = 10, string $orderBy = 'ASC'): array
    {
        $startOfDay = (clone $endingDate)->setTime(0, 0, 0);
        $endOfDay = (clone $endingDate)->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'c')->addSelect('c')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.ending_date >= :startOfDay')
            ->andWhere('b.ending_date <= :endOfDay')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBookingsByCreationDate(DateTimeImmutable $creationDate, int $limit = 10, string $orderBy = 'ASC'): array
    {
        $startOfDay = (clone $creationDate)->setTime(0, 0, 0);
        $endOfDay = (clone $creationDate)->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'c')->addSelect('c')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.created_at >= :startOfDay')
            ->andWhere('b.created_at <= :endOfDay')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBookingsByStatus(BookingStatus $status, int $limit = 10, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'c')->addSelect('c')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.status = :status')
            ->setParameter('status', $status->value)
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBookingsByClientId(int $id, int $limit = 10, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'c')->addSelect('c')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->andWhere('b.id = :clientId')
            ->setParameter('clientId', $id)
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countBookingsByPeriod(DateTimeImmutable $start, DateTimeImmutable $end, BookingStatus $status = BookingStatus::CONFIRMED): int
    {
        $startFormatted =  $start->setTime(00, 00, 00);
        $endFormatted = $end->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.createdAt BETWEEN :start AND :end')
            ->andWhere('b.status = :status')
            ->setParameter('start', $startFormatted)
            ->setParameter('end', $endFormatted)
            ->setParameter('status', $status->value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCancelationByPeriod(DateTimeImmutable $start, DateTimeImmutable $end, BookingStatus $status = BookingStatus::CANCELLED): int
    {
        $startFormatted =  $start->setTime(0, 0, 0);
        $endFormatted = $end->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.updatedAt BETWEEN :start AND :end')
            ->andWhere('b.status = :status')
            ->setParameter('start', $startFormatted)
            ->setParameter('end', $endFormatted)
            ->setParameter('status', $status->value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotalAmountByPeriod(DateTimeImmutable $start, DateTimeImmutable $end, BookingStatus $status = BookingStatus::CONFIRMED): float
    {
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
