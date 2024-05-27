<?php

namespace App\Repository;

use App\Entity\Actividades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Actividades>
 *
 * @method Actividades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Actividades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Actividades[]    findAll()
 * @method Actividades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActividadesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actividades::class);
    }

//    /**
//     * @return Actividades[] Returns an array of Actividades objects
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

//    public function findOneBySomeField($value): ?Actividades
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    public function findActividades()
    {
        return $this->createQueryBuilder('a')
        ->select('a.id, a.nombre, a.descripcion, a.fecha, a.direccion, a.municipio, a.img, a.hora, u.id as id_usuario, u.nick as nick, p.foto as foto_perfil, 
                  (CASE WHEN a.fecha >= CURRENT_DATE() THEN 0 ELSE 1 END) AS is_past')
        ->leftJoin('a.actividadesUsuarios', 'au')
        ->leftJoin('au.nick', 'u')
        //->where('a.fecha >= CURRENT_DATE()')
        ->leftJoin('u.perfiles', 'p')
        ->orderBy('is_past', 'ASC')  // Primero actividades futuras (is_past = 0), luego pasadas (is_past = 1)
        ->addOrderBy('a.fecha', 'ASC', 'is_past = 0')  // Orden ascendente por fecha para actividades futuras
        ->addOrderBy('a.fecha', 'DESC', 'is_past = 1')  // Orden descendente por fecha para actividades pasadas
        ->groupBy('a.id')
        ->getQuery()
        ->getResult();

    }
    public function findActividad($id)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id, a.nombre, a.descripcion, a.fecha, a.direccion, a.municipio, a.hora, a.img, a.npersonas, u.nick as nick, u.id as idusuario, p.foto as foto_perfil')
            ->leftJoin('a.actividadesUsuarios', 'au', 'WITH', 'au.id_actividad = a.id AND au.creador = :esCreador')
            ->leftJoin('au.nick', 'u')
            ->leftJoin('u.perfiles', 'p')
            ->where('a.id = :idActividad')
            ->groupBy('a.id')
            ->setParameter('idActividad', $id)
            ->setParameter('esCreador', true)
            ->getQuery()
            ->getResult();
    }

}
