<?php

namespace App\Repository;

use App\Entity\Usuarios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usuarios>
 *
 * @method Usuarios|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuarios|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuarios[]    findAll()
 * @method Usuarios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuariosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuarios::class);
    }

    public function isCredentialsValid($username, $password, $entityManager)
{
    
    $user = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $username]);
   
    if ($user && password_verify($password, $user->getPassword())) {
        $user->setOnline(true);
        $entityManager->flush(); 
        return true;
    }else{

        return false; 
    }

}


//    /**
//     * @return Usuarios[] Returns an array of Usuarios objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Usuarios
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findUsuariosByActividad($id)
        {
            return $this->createQueryBuilder('u')
                ->select('u.id, u.nick, p.foto as foto_perfil')
                ->leftJoin('u.perfiles', 'p')
                ->innerJoin('u.actividadesUsuarios', 'au')
                ->innerJoin('au.id_actividad', 'a')
                ->where('a.id = :idActividad')
                ->setParameter('idActividad', $id)
                ->getQuery()
                ->getResult();
        }
}
