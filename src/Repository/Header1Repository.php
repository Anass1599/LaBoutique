<?php

namespace App\Repository;

use App\Entity\Header1;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Header1|null find($id, $lockMode = null, $lockVersion = null)
 * @method Header1|null findOneBy(array $criteria, array $orderBy = null)
 * @method Header1[]    findAll()
 * @method Header1[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Header1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Header1::class);
    }

    // /**
    //  * @return Header1[] Returns an array of Header1 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Header1
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
