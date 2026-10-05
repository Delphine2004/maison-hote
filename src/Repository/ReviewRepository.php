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

    public function findReviewsToApprouve(): array
    {
        return $this->createQueryBuilder('re')
            ->andWhere('re.status = :status')
            ->setParameter('status', ReviewStatus::PENDING->value)
            ->getQuery()
            ->getResult();
    }


    public function findReviewByFilters(
        ?searchReviewDTO $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $qb = $this->createQueryBuilder('re')
            ->leftJoin('re.user', 'u')->addSelect('u')
            ->leftJoin('re.room', 'ro')->addSelect('ro');

        // Review Id
        if ($criteria->getReviewId()) {
            $qb->andWhere('re.id = :id')
                ->setParameter('id', $criteria->getReviewId());
        }

        // Rate
        if ($criteria->getRate()) {
            $qb->andWhere('re.id = :id')
                ->setParameter('id', $criteria->getRate());
        }

        // Room Id
        if ($criteria->getRoomId()) {
            $qb->andWhere('re.id = :id')
                ->setParameter('id', $criteria->getRoomId());
        }

        // User Id
        if ($criteria->getUserId()) {
            $qb->andWhere('re.id = :id')
                ->setParameter('id', $criteria->getUserId());
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
}
