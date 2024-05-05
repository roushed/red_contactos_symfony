<?php

namespace App\Repository;

use App\Entity\ActividadesUsuarios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActividadesUsuarios>
 *
 * @method ActividadesUsuarios|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActividadesUsuarios|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActividadesUsuarios[]    findAll()
 * @method ActividadesUsuarios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActividadesUsuariosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActividadesUsuarios::class);
    }

//    /**
//     * @return ActividadesUsuarios[] Returns an array of ActividadesUsuarios objects
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

//    public function findOneBySomeField($value): ?ActividadesUsuarios
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
