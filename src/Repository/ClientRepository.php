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


    public function findClientById(int $id): ?Client
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c', 'b')
            ->leftJoin('c.bookings', 'b')->addSelect('b')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    public function findClientByEmail(string $email): ?Client
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c', 'b')
            ->leftJoin('c.bookings', 'b')->addSelect('b')
            ->andWhere('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
