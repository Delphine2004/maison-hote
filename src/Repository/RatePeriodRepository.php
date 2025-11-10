<?php

namespace App\Repository;

use App\Entity\RatePeriod;
use App\Enum\RatePeriodCategory;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RatePeriod>
 */
class RatePeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatePeriod::class);
    }

    public function findSpecificationByCategory(RatePeriodCategory $category, int $limit = 10, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('rp')
            ->andWhere('rp.category = :category')
            ->setParameter('category', $category->value)
            ->orderBy('rp.id',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
