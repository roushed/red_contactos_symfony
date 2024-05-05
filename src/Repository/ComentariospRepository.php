<?php

namespace App\Repository;

use App\Entity\Comentariosp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comentariosp>
 *
 * @method Comentariosp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comentariosp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comentariosp[]    findAll()
 * @method Comentariosp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComentariospRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comentariosp::class);
    }

//    /**
//     * @return Comentariosp[] Returns an array of Comentariosp objects
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

//    public function findOneBySomeField($value): ?Comentariosp
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function getComentarios(int $id): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.texto, c.fecha, u.nick, pf.foto')
            ->leftJoin('c.post', 'p')
            ->leftJoin('c.nick', 'u')
            ->leftJoin('u.perfil', 'pf')
            ->where('p.id = :postId')
            ->setParameter('postId', $id)
            ->getQuery()
            ->getResult();
    }
}
