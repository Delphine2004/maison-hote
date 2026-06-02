<?php

namespace App\Repository;

use App\Entity\User;
use App\DTO\SearchClient;

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
        ?SearchClient $criteria,
        int $limit = 10,
        string $orderBy = 'DESC'
    ): array {
        $qb = $this->createQueryBuilder('u');

        if ($criteria->getUserId()) {
            $qb->andWhere('u.id = :id')
                ->setParameter('id', $criteria->getUserId());
        }

        if ($criteria->getLastName()) {
            $qb->andWhere('u.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $criteria->getLastName() . '%');
        }

        if ($criteria->getEmail()) {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $criteria->getEmail() . '%');
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
