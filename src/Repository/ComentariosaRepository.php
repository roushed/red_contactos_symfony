<?php

namespace App\Repository;

use App\Entity\Comentariosa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comentariosa>
 *
 * @method Comentariosa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comentariosa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comentariosa[]    findAll()
 * @method Comentariosa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComentariosaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comentariosa::class);
    }

//    /**
//     * @return Comentariosa[] Returns an array of Comentariosa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comentariosa
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function getComentariosActividad(int $id): array
{
    return $this->createQueryBuilder('c')
        ->select('c.texto, c.fecha, u.nick, pf.foto')
        ->leftJoin('c.actividad', 'a')
        ->leftJoin('c.nick', 'u')
        ->leftJoin('u.perfil', 'pf')
        ->where('a.id = :actividadId')
        ->setParameter('actividadId', $id)
        ->getQuery()
        ->getResult();
}
}
