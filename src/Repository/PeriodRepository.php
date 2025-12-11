<?php

namespace App\Repository;

use App\Entity\Period;

use DateTimeImmutable;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class PeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Period::class);
    }

    public function findActivePeriod(
        int $limit = 10,
        string $orderBy = 'ASC'
    ): array {
        $now = new DateTimeImmutable('today');

        return $this->createQueryBuilder('p')
            ->leftJoin('p.rates', 'ra')->addSelect('ra')
            ->where('p.endingDate >= :now')
            ->setParameter('now', $now)
            ->orderBy('p.endingDate',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
