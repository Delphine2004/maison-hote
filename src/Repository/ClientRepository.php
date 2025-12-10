<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findClientByFilters(
        ?array $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->select('DISTINCT c', 'b')
            ->leftJoin('c.bookings', 'b')->addSelect('b');

        if (!empty($criteria['id'])) {
            $qb->andWhere('c.id = :id')
                ->setParameter('id', $criteria['id']);
        }
        if (!empty($criteria['lastName'])) {
            $qb->andWhere('c.lastName = :lastName')
                ->setParameter('lastName', $criteria['lastName']);
        }
        if (!empty($criteria['email'])) {
            $qb->andWhere('c.email = :email')
                ->setParameter('email', $criteria['email']);
        }
        return $qb->orderBy('c.id',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
