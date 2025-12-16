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
