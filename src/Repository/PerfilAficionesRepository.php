<?php

namespace App\Repository;

use App\Entity\PerfilAficiones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PerfilAficiones>
 *
 * @method PerfilAficiones|null find($id, $lockMode = null, $lockVersion = null)
 * @method PerfilAficiones|null findOneBy(array $criteria, array $orderBy = null)
 * @method PerfilAficiones[]    findAll()
 * @method PerfilAficiones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerfilAficionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PerfilAficiones::class);
    }

//    /**
//     * @return PerfilAficiones[] Returns an array of PerfilAficiones objects
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

//    public function findOneBySomeField($value): ?PerfilAficiones
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
