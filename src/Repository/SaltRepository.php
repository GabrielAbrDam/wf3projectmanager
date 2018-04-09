<?php

namespace App\Repository;

use App\Entity\Salt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Salt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Salt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Salt[]    findAll()
 * @method Salt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaltRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Salt::class);
    }

//    /**
//     * @return Salt[] Returns an array of Salt objects
//     */
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
    public function findOneBySomeField($value): ?Salt
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
