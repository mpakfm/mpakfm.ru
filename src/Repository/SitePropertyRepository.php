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

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveItem(SiteProperty $item)
    {
        $this->_em->persist($item);
        $this->_em->flush();
    }
}
