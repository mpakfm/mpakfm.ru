<?php

namespace App\Repository;

use App\Entity\SiteProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SiteProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteProperty[]    findAll()
 * @method SiteProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SitePropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteProperty::class);
    }

    // /**
    //  * @return SiteProperty[] Returns an array of SiteProperty objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SiteProperty
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
