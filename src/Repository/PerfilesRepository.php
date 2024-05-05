<?php

namespace App\Repository;

use App\Entity\Perfiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Perfiles>
 *
 * @method Perfiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Perfiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Perfiles[]    findAll()
 * @method Perfiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerfilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Perfiles::class);
    }

//    /**
//     * @return Perfiles[] Returns an array of Perfiles objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Perfiles
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
