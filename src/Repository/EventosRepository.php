<?php

namespace App\Repository;

use App\Entity\Eventos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Eventos>
 *
 * @method Eventos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eventos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eventos[]    findAll()
 * @method Eventos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eventos::class);
    }

//    /**
//     * @return Eventos[] Returns an array of Eventos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Eventos
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
