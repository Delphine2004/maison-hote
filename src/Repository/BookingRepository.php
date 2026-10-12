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
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findBookingsByFilters(
        ?SearchBookingDTO $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {

        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r');

        // Booking Id
        if ($criteria->getBookingId()) {
            $qb->andWhere('b.id = :id')
                ->setParameter('id', $criteria->getBookingId());
        }

        // Nom client
        if ($criteria->getLastName()) {
            $qb->andWhere('u.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $criteria->getLastName() . '%');
        }

        // Booking Statut
        if ($criteria->getStatus()) {
            $qb->andWhere('b.status = :status')
                ->setParameter('status', $criteria->getStatus()->value);
        }

        // Dates
        if ($criteria->getStartingDate()) {
            $date = $criteria->getStartingDate();

            $start = (clone $date)->setTime(0, 0, 0);
            $end   = (clone $date)->setTime(23, 59, 59);

            $qb->andWhere('b.startingDate BETWEEN :startDateStart AND :startDateEnd')
                ->setParameter('startDateStart', $start)
                ->setParameter('startDateEnd', $end);
        }

        if ($criteria->getEndingDate()) {
            $date = $criteria->getEndingDate();

            $start = (clone $date)->setTime(0, 0, 0);
            $end   = (clone $date)->setTime(23, 59, 59);

            $qb->andWhere('b.endingDate BETWEEN :endDateStart AND :endDateEnd')
                ->setParameter('endDateStart', $start)
                ->setParameter('endDateEnd', $end);
        }

        if ($criteria->getCreatedAt()) {
            $date = $criteria->getCreatedAt();

            $start = (clone $date)->setTime(0, 0, 0);
            $end   = (clone $date)->setTime(23, 59, 59);

            $qb->andWhere('b.createdAt BETWEEN :createdAtStart AND :createdAtEnd')
                ->setParameter('createdAtStart', $start)
                ->setParameter('createdAtEnd', $end);
        }

        return $qb->orderBy('b.id',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function hasCurrentReservation(User $user): bool
    {
        $now = new DateTimeImmutable();

        return (bool) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.user = :user')
            ->andWhere(':now BETWEEN b.startingDate AND b.endingDate')
            ->andWhere('b.status = :status')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->setParameter('status', BookingStatus::IN->value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findOverlappingBookings(
        Room $room,
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ): array {
        return $this->createQueryBuilder('b')
            ->where('b.room = :room')
            ->andWhere('b.startingDate < :end')
            ->andWhere('b.endingDate > :start')
            ->setParameter('room', $room)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingBookingsByClient(
        int $userId,
        int $limit = 10,
        string $orderBy = 'ASC'
    ): array {
        $today = new DateTimeImmutable('today');
        $start = $today->setTime(0, 0, 0);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.user = :userId')
            ->andWhere('b.endingDate >= :start')
            ->andWhere('b.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('start', $start)
            ->setParameter('status', BookingStatus::CONFIRMED->value)
            ->orderBy('b.startingDate', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPastBookingsByClient(
        int $userId,
        int $limit = 10,
        string $orderBy = 'ASC'
    ): array {
        $today = new DateTimeImmutable('today');
        $end = $today->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.user = :userId')
            ->andWhere('b.endingDate < :end')
            ->setParameter('userId', $userId)
            ->setParameter('end', $end)
            ->orderBy('b.startingDate', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findTodayBookings(
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $today = new DateTimeImmutable('today');

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.startingDate >= :today')
            ->setParameter('today', $today)
            ->orderBy('b.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findInHouse(
        string $orderBy = 'DESC'
    ): array {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.status = :status')
            ->setParameter('status', BookingStatus::IN->value)
            ->orderBy('b.id', $orderBy)
            ->getQuery()
            ->getResult();
    }

    public function findCheckOutsForDay(
        DateTimeImmutable $day,
        string $orderBy = 'DESC'
    ): array {
        $start = $day->setTime(0, 0, 0);
        $end   = $day->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.status = :status')
            ->andWhere('b.endingDate BETWEEN :start AND :end')
            ->setParameter('status', BookingStatus::IN->value)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('b.id', $orderBy)
            ->getQuery()
            ->getResult();
    }

    public function findCheckInsForDay(
        DateTimeImmutable $day,
        string $orderBy = 'DESC'
    ): array {
        $start = $day->setTime(0, 0, 0);
        $end   = $day->setTime(23, 59, 59);

        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')->addSelect('u')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.status = :status')
            ->andWhere('b.startingDate BETWEEN :start AND :end')
            ->setParameter('status', BookingStatus::CONFIRMED->value)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('b.id', $orderBy)
            ->getQuery()
            ->getResult();
    }

    public function findOutOfOrder(
        string $orderBy = 'DESC'
    ): array {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.room', 'r')->addSelect('r')
            ->where('b.status = :status')
            ->setParameter('status', BookingStatus::OUTOFORDER->value)
            ->orderBy('b.id', $orderBy)
            ->getQuery()
            ->getResult();
    }

    public function findBookingWithoutReviewsByClient(
        int $userId,
        string $orderBy = 'DESC'
    ): array {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.review', 'r')
            ->where('b.status = :status')
            ->andWhere('b.user = :user')
            ->andWhere('r.id IS NULL')
            ->setParameter('status', BookingStatus::FINALIZED->value)
            ->setParameter('user', $userId)
            ->orderBy('b.id', $orderBy)
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
