<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUserByFilters(
        ?array $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $qb = $this->createQueryBuilder('u')
            ->select('DISTINCT u', 'b')
            ->leftJoin('u.bookings', 'b')->addSelect('b');

        if (!empty($criteria['id'])) {
            $qb->andWhere('u.id = :id')
                ->setParameter('id', $criteria['id']);
        }
        if (!empty($criteria['lastName'])) {
            $qb->andWhere('u.lastName = :lastName')
                ->setParameter('lastName', $criteria['lastName']);
        }
        if (!empty($criteria['email'])) {
            $qb->andWhere('u.email = :email')
                ->setParameter('email', $criteria['email']);
        }
        return $qb->orderBy('u.id',  $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    public function findUsersWithoutRoles(
        array $roles,
        int $limit = 10,
        string $orderBy = 'ASC'
    ): array {
        $qb = $this->createQueryBuilder('u');

        foreach ($roles as $index => $role) {
            $qb->andWhere("u.roles NOT LIKE :role_$index")
                ->setParameter("role_$index", '%"' . $role . '"%');
        }

        return $qb
            ->orderBy('u.id', $orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
