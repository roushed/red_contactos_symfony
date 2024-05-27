<?php

namespace App\Repository;

use App\Entity\Mensajes;
use App\Entity\Usuarios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mensajes>
 *
 * @method Mensajes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mensajes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mensajes[]    findAll()
 * @method Mensajes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MensajesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mensajes::class);
    }

    public function findUsuariosConUltimosMensajes(int $usuarioId): array
    {
        $qb = $this->createQueryBuilder('m');

$subQuery = $this->createQueryBuilder('sub')
    ->select('MAX(sub.id) as maxId')
    ->andWhere('sub.nickrecibo = :usuarioId')
    ->groupBy('sub.nickenvia')
    ->getDQL();

$result = $qb
    ->select('m', 'u.id as userId', 'u.nick as usuarioNick', 'per.foto as perfilFoto', 'm.texto as textoMensaje', 'm.fecha as fechaMensaje')
    ->innerJoin('m.nickenvia', 'u')
    ->innerJoin('u.perfiles', 'per')
    ->andWhere($qb->expr()->in('m.id', $subQuery))
    ->setParameter('usuarioId', $usuarioId)
    ->orderBy('m.id', 'DESC')
    ->getQuery()
    ->getResult();

return $result;
    }

    public function findUsuariosInteractuados(Usuarios $usuarioActual): array
    {
        $qb = $this->createQueryBuilder('m');

        $subQuery1 = $this->_em->createQueryBuilder()
            ->select('DISTINCT IDENTITY(m3.nickenvia)')
            ->from('App\Entity\Mensajes', 'm3')
            ->where('m3.nickrecibo = :usuarioActual')
            ->getDQL();
    
        $subQuery2 = $this->_em->createQueryBuilder()
            ->select('DISTINCT IDENTITY(m4.nickrecibo)')
            ->from('App\Entity\Mensajes', 'm4')
            ->where('m4.nickenvia = :usuarioActual')
            ->getDQL();
    
        // Subquery to check for blocked users
        $subQueryBlocked = $this->_em->createQueryBuilder()
            ->select('c.id')
            ->from('App\Entity\Contactos', 'c')
            ->where('c.bloqueado = true AND (c.usuario = :usuarioActual AND c.contacto = u OR c.usuario = u AND c.contacto = :usuarioActual)')
            ->getDQL();
    
        $qb->select('u, p, p.foto, (
                SELECT COUNT(m2.id) 
                FROM App\Entity\Mensajes m2 
                WHERE m2.nickenvia = u AND m2.nickrecibo = :usuarioActual AND m2.leido = false
            ) AS mensajes_no_leidos')
            ->from('App\Entity\Usuarios', 'u')
            ->leftJoin('u.perfiles', 'p')
            ->where($qb->expr()->orX(
                $qb->expr()->in('u.id', $subQuery1),
                $qb->expr()->in('u.id', $subQuery2)
            ))
            ->andWhere('u != :usuarioActual')
            ->andWhere($qb->expr()->not(
                $qb->expr()->exists($subQueryBlocked)
            ))
            ->setParameter('usuarioActual', $usuarioActual);
    
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Mensajes[] Returns an array of Mensajes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mensajes
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findMensajesUsuario($usuario, $selectedUserId)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.nickenvia = :usuarioId AND m.nickrecibo = :selectedUserId) OR (m.nickenvia = :selectedUserId AND m.nickrecibo = :usuarioId)')
            ->setParameter('usuarioId', $usuario)
            ->setParameter('selectedUserId', $selectedUserId)
            ->orderBy('m.fecha', 'ASC')
            ->getQuery()
            ->getResult();
    }



    public function findConversacion($usuarioActual, $usuarioSeleccionado)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.nickenvia = :usuarioId AND m.nickrecibo = :selectedUserId) OR (m.nickenvia = :selectedUserId AND m.nickrecibo = :usuarioId)')
            ->setParameter('usuarioId', $usuarioActual)
            ->setParameter('selectedUserId', $usuarioSeleccionado)
            ->orderBy('m.fecha', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countMensajesNoLeidos($usuarioActual)
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.nickrecibo = :usuario')
            ->andWhere('m.leido = :leido')
            ->setParameter('usuario', $usuarioActual)
            ->setParameter('leido', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
