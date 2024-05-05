<?php

namespace App\Repository;

use App\Entity\Aficiones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Aficiones>
 *
 * @method Aficiones|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aficiones|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aficiones[]    findAll()
 * @method Aficiones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AficionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aficiones::class);
    }

//    /**
//     * @return Aficiones[] Returns an array of Aficiones objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Aficiones
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
