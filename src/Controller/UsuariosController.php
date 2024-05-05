<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Perfiles;
use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use DateInterval;
use DateTime;


class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'app_usuarios')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $afinidad=array();
        if($session->get('user_authenticated')){

            $queryBuilder = $entityManager->createQueryBuilder();
            $aficionesUsuarioAutenticado = $queryBuilder

                ->select('af.nombre')
                ->from('App\Entity\Aficiones', 'af')
                ->join('af.perfilAficiones', 'pa')
                ->join('pa.perfil', 'p')
                ->join('p.nick', 'u') 
                ->where('u.nick = :userNick')
                ->setParameter('userNick', $session->get('nombre'))
                ->getQuery()
                ->getResult();
           
            $queryBuilder = $entityManager->createQueryBuilder();
            $usuariosa = $queryBuilder
            ->select('u.nick', 'u.id', 'u.online', 'p.edad', 'p.foto', 'u.online')
            ->from(Perfiles::class, 'p')
            ->leftJoin('p.nick', 'u')
            ->where("u.nick <> '" . $session->get('nombre') . "'") 
            ->getQuery()
            ->getResult();
             
            foreach ($usuariosa as $usuario) {
                $coincidencias = 0;
                $aficionesUsuario = $entityManager->createQueryBuilder()
                ->select('af.nombre')
                ->from('App\Entity\Aficiones', 'af')
                ->join('af.perfilAficiones', 'pa')
                ->join('pa.perfil', 'p')
                ->where('p.id = :userId')
                ->setParameter('userId', $usuario['id'])
                ->getQuery()
                ->getResult();
        
                foreach ($aficionesUsuario as $aficionUsuario) {
                    $nombreAficion = strtolower($aficionUsuario['nombre']);
                    $nombresAutenticados = array_map(function ($item) {
                        return strtolower($item['nombre']);
                    }, $aficionesUsuarioAutenticado);
                
                    if (in_array($nombreAficion, $nombresAutenticados)) {

                        $coincidencias++;
                    }
                }
                
                if($coincidencias != 0){

                    $afinidad[] = array(
                    'id' => $usuario['id'],
                    'nick' => $usuario['nick'],
                    'foto' => $usuario['foto'],
                    'edad' => $usuario['edad'],
                    'online' => $usuario['online'],
                    'coincidencias' => $coincidencias,
                    );
                }
            }
                            
            usort($afinidad, function ($a, $b) {
                return $b['coincidencias'] - $a['coincidencias'];
            });

       
        }
        
        
        $queryBuilder = $entityManager->createQueryBuilder();
        $usuarios = $queryBuilder
            ->select('u.nick', 'u.id', 'u.online', 'p.edad', 'p.foto')
            ->from(Perfiles::class, 'p')
            ->leftJoin('p.nick', 'u') 
            ->orderBy('u.online', 'DESC')
            ->getQuery()
            ->getResult();
       
        return $this->render('usuarios/index.html.twig', [
            'usuarios' => $usuarios,
            'afinidades' => $afinidad,
            
        ]);
    }

    #[Route('/usuarios/ver/{id}', name:'app_usuarios_ver', methods:['GET', 'POST'])]

    public function ver(int $id, EntityManagerInterface $entityManager): Response{
        
        $queryBuilder = $entityManager->createQueryBuilder();
        $aficiones = $queryBuilder
            ->select('af.id, af.nombre')
            ->from('App\Entity\Aficiones', 'af')
            ->join('af.perfilAficiones', 'pa')
            ->join('pa.perfil', 'p')
            ->where('p.nick = :idUsuario')
            ->setParameter('idUsuario', $id)
            ->getQuery()
            ->getResult();

        $queryBuilder = $entityManager->createQueryBuilder();
        $usuario = $queryBuilder
        ->select('u.id', 'u.nick', 'u.online', 'u.fecha', 'p.edad', 'p.genero', 'p.ciudad', 'p.descripcion','p.foto')
        ->from(Perfiles::class, 'p')
        ->leftJoin('p.nick', 'u')
        ->where('p.id = :perfil_id')  
        ->setParameter('perfil_id', $id) 
        ->getQuery()
        ->getResult();
        $ultimaConexion = $usuario[0]['fecha'];
        $hoy = new DateTime();
        $diff = $hoy->diff($ultimaConexion);

        if ($diff->d == 0) {
            $ultimaV = "hoy";
        } elseif ($diff->d == 1) {
            $ultimaV = "ayer";
        } elseif ($diff->d < 7) {
            $ultimaV = $diff->d . " días";
        } elseif ($diff->m == 0) {
            $weeks = floor($diff->d / 7);
            $ultimaV= ($weeks == 1) ? "1 semana" : $weeks ." semanas";
        } else {
            $ultimaV = null; 
        }
        

        return $this -> render('usuarios/ver.html.twig',[
            'results' => $usuario,
            'aficiones' => $aficiones,
            'ultima_v' => $ultimaV,
            
        ]);

    }
}
