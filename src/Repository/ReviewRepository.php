<?php

namespace App\Repository;

use App\Entity\Review;
use App\DTO\SearchReviewDTO;
use App\Enum\ReviewStatus;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findReviewsToApprove(): array
    {
        return $this->createQueryBuilder('re')
            ->where('re.status = :status')
            ->setParameter('status', ReviewStatus::PENDING->value)
            ->getQuery()
            ->getResult();
    }

    public function findApprovedReviews(): array
    {
        return $this->createQueryBuilder('re')
            ->where('re.status = :status')
            ->setParameter('status', ReviewStatus::CONFIRMED->value)
            ->getQuery()
            ->getResult();
    }


    public function findReviewByFilters(
        ?searchReviewDTO $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $qb = $this->createQueryBuilder('re')
            ->leftJoin('re.booking', 'b')->addSelect('b');

        // Review Id
        if ($criteria->getReviewId()) {
            $qb->andWhere('re.id = :id')
                ->setParameter('id', $criteria->getReviewId());
        }

        // Rate
        if ($criteria->getRate()) {
            $qb->andWhere('re.rate = :rate')
                ->setParameter('rate', $criteria->getRate());
        }

        // Status
        if ($criteria->getStatus()) {
            $qb->andWhere('re.status = :status')
                ->setParameter('status', $criteria->getStatus()->value);
        }

        // Room
        if ($criteria->getRoom()) {
            $qb->andWhere('b.room = :room')
                ->setParameter('room', $criteria->getRoom());
        }

        if ($criteria->getCreatedAt()) {
            $date = $criteria->getCreatedAt();

            $start = (clone $date)->setTime(0, 0, 0);
            $end   = (clone $date)->setTime(23, 59, 59);

            $qb->andWhere('re.createdAt BETWEEN :createdAtStart AND :createdAtEnd')
                ->setParameter('createdAtStart', $start)
                ->setParameter('createdAtEnd', $end);
        }

        return $qb->orderBy('re.id',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    public function getTotalApprovedReviews(): int
    {
        return (int) $this->createQueryBuilder('re')
            ->select('COUNT(re.id)')
            ->where('re.status = :status')
            ->setParameter('status', ReviewStatus::CONFIRMED->value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAverageRating(): float
    {
        return (float) $this->createQueryBuilder('re')
            ->select('AVG(re.rate)')
            ->where('re.status = :status')
            ->setParameter('status', ReviewStatus::CONFIRMED->value)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
}
