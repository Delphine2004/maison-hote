<?php

namespace App\Repository;

use App\Entity\User;
use App\Enum\UserRole;
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

    public function findClientByFilters(
        ?array $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $qb = $this->createQueryBuilder('u')
            ->select('DISTINCT u', 'b')
            ->leftJoin('u.bookings', 'b');

        if (!empty($criteria['user_id'])) {
            $qb->andWhere('u.id = :id')
                ->setParameter('id', $criteria['user_id']);
        }
        if (!empty($criteria['last_name'])) {
            $qb->andWhere('u.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $criteria['last_name'] . '%');
        }
        if (!empty($criteria['email'])) {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $criteria['email'] . '%');
        }
        // filtre clients
        $qb->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_CLIENT%');
        return $qb->orderBy('u.id', $orderBy)

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
