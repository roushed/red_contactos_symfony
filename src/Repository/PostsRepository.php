<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Usuarios;
use App\Entity\Imagenes;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

//    /**
//     * @return Posts[] Returns an array of Posts objects
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

//    public function findOneBySomeField($value): ?Posts
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



public function findBySubjectAndCategory(string $textoBusqueda, int $idCategoria): array
{

    if($idCategoria != 7 && $idCategoria != 8){

        return $this->createQueryBuilder('p')
            ->select('p', 'u.nick', 'p.fecha', 'p.precio', 'pf.foto')
            ->leftJoin('p.nick', 'u')
            ->leftJoin('u.perfil', 'pf')
            ->andWhere('p.subject LIKE :textoSubject')
            ->orWhere('p.texto LIKE :textoTexto')
            ->andWhere('p.categoria = :categoria')
            ->setParameter('textoSubject', '%'.$textoBusqueda.'%')
            ->setParameter('textoTexto', '%'.$textoBusqueda.'%')
            ->setParameter('categoria', $idCategoria)
            ->getQuery()
            ->getResult();

    }else{

        $subqueryBuilder = $this->createQueryBuilder('p2');
        $subquery = $subqueryBuilder
            ->select('MIN(im2.id)')
            ->leftJoin('p2.imagenes', 'im2')
            ->where('p2.id = p.id')
            ->getDQL();

        return $this->createQueryBuilder('p')
            ->select('p', 'u.nick', 'p.fecha', 'p.precio', 'pf.foto', 'p.subject', 'p.adquisicion', 'p.municipio', 'p.adquisicion', 'im.nombre AS primera_imagen')
            ->leftJoin('p.nick', 'u')
            ->leftJoin('u.perfil', 'pf')
            ->leftJoin(Imagenes::class, 'im', Join::WITH, 'im.post = p')
            ->andWhere('p.subject LIKE :textoSubject')
            ->orWhere('p.texto LIKE :textoTexto')
            ->orWhere('p.municipio LIKE :textoMunicipio')
            ->andWhere('p.categoria = :categoria')
            ->andWhere(
                $this->createQueryBuilder('p')
                    ->expr()
                    ->in('im.id', $subquery)
            )
            ->setParameter('textoSubject', '%'.$textoBusqueda.'%')
            ->setParameter('textoTexto', '%'.$textoBusqueda.'%')
            ->setParameter('textoMunicipio', '%'.$textoBusqueda.'%')
            ->setParameter('categoria', $idCategoria)
            ->getQuery()
            ->getResult();



    }
    
}

public function findAnunciosByCategoria(int $idCategoria): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $anuncios = $queryBuilder
        ->select([
            'p.adquisicion',
            'p.id',
            'p.subject',
            'p.texto',
            'p.fecha',
            'p.precio',
            'p.municipio',
            'IDENTITY(p.nick) AS usuario_id', 
            'u.nick as usuario_nick',
            'pf.foto as perfil_foto',
            '(SELECT COUNT(c.id) FROM App\Entity\Comentariosp c WHERE c.post = p) AS cantidad_comentarios',
            'im.nombre AS primera_imagen' // Agregamos el alias para la primera imagen
        ])
       
        ->leftJoin('p.nick', 'u')
        ->leftJoin('u.perfiles', 'pf')
        ->leftJoin(Imagenes::class, 'im', 'WITH', 'im.post = p') // Unimos la entidad Imagenes con Posts
        ->where('p.categoria = :categoria')
        ->andWhere(
            $queryBuilder->expr()->eq(
                'im.id',
                '(SELECT MIN(im2.id) FROM App\Entity\Imagenes im2 WHERE im2.post = p)'
            )
        )
        ->setParameter('categoria', $idCategoria)
        ->orderBy('p.fecha', 'DESC')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            return $anuncios;
    }


    public function findByAdquisicionAndCategory(string $adquisicion, int $idCategoria): array
    {
        
        if($adquisicion == 0 ||  $adquisicion == 1){
            $subqueryBuilder = $this->createQueryBuilder('p2');
            $subquery = $subqueryBuilder
                ->select('MIN(im2.id)')
                ->leftJoin('p2.imagenes', 'im2')
                ->where('p2.id = p.id')
                ->getDQL();
    
            return $this->createQueryBuilder('p')
                ->select('p', 'u.nick', 'p.fecha', 'p.precio', 'p.adquisicion', 'p.municipio', 'pf.foto', 'im.nombre AS primera_imagen')
                ->leftJoin('p.nick', 'u')
                ->leftJoin('u.perfil', 'pf')
                ->leftJoin(Imagenes::class, 'im', Join::WITH, 'im.post = p')
                ->andWhere('p.adquisicion = :adquisicion')
                ->andWhere('p.categoria = :categoria')
                ->andWhere(
                    $this->createQueryBuilder('p')
                        ->expr()
                        ->in('im.id', $subquery)
                )
                ->setParameter('adquisicion', $adquisicion)
                ->setParameter('categoria', $idCategoria)
                ->getQuery()
                ->getResult();

        }else{

            $subqueryBuilder = $this->createQueryBuilder('p2');
            $subquery = $subqueryBuilder
                ->select('MIN(im2.id)')
                ->leftJoin('p2.imagenes', 'im2')
                ->where('p2.id = p.id')
                ->getDQL();
    
            return $this->createQueryBuilder('p')
                ->select('p', 'u.nick', 'p.fecha', 'p.precio', 'p.adquisicion', 'p.municipio', 'pf.foto', 'im.nombre AS primera_imagen')
                ->leftJoin('p.nick', 'u')
                ->leftJoin('u.perfil', 'pf')
                ->leftJoin(Imagenes::class, 'im', Join::WITH, 'im.post = p')
                ->andWhere('p.categoria = :categoria')
                ->andWhere(
                    $this->createQueryBuilder('p')
                        ->expr()
                        ->in('im.id', $subquery)
                )
                ->setParameter('categoria', $idCategoria)
                ->orderBy('p.fecha', 'DESC')
                ->orderBy('p.id', 'DESC')
                ->getQuery()
                ->getResult();


        }


    }


    public function findAnunciosById(int $idCategoria): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $anuncios = $queryBuilder
            ->select([
                'p.id',
                'p.subject',
                'p.texto',
                'p.fecha',
                'p.precio',
                'IDENTITY(p.nick) AS usuario_id', 
                'u.nick as usuario_nick',
                'pf.foto as perfil_foto',
                '(SELECT COUNT(c.id) FROM App\Entity\Comentariosp c WHERE c.post = p) AS cantidad_comentarios'
            ])
           
            ->leftJoin('p.nick', 'u')
            ->leftJoin('u.perfiles', 'pf')
            ->where('p.categoria = :categoria')
            ->setParameter('categoria', $idCategoria)
            ->orderBy('p.fecha', 'DESC')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY); 

            return $anuncios;
    }


    public function findPosts($offset, $limit)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id', 'p.subject', 'p.texto', 'p.creador', 'p.fecha', 'IDENTITY(p.nick) AS usuario_id', 'u.nick as usuario_nick', 'c.nombre as categoria_nombre', 'pf.foto as perfil_foto')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.nick', 'u')
            ->leftJoin('u.perfiles', 'pf')
            ->orderBy('p.fecha', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }



}
