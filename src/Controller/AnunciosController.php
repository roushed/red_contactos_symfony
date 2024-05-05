<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Usuarios;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PostsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AnunciosController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(PostsRepository $postsRepository, EntityManagerInterface $entityManager) {
    $this->postsRepository = $postsRepository;
    $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_listar')]
    public function index(EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {

    $postsRepository = $this->entityManager->getRepository(Posts::class);   
    $page = $request->query->getInt('page', 1);
    $limit = 10; 
    $offset = ($page - 1) * $limit;

    
    $totalPosts = $entityManager->createQueryBuilder()
        ->select('COUNT(p.id)')
        ->from(Posts::class, 'p')
        ->getQuery()
        ->getSingleScalarResult();

  
    $totalPages = ceil($totalPosts / $limit);

    
    $posts = $entityManager->createQueryBuilder()
        ->select('p.id', 'p.subject', 'p.texto', 'p.imagen', 'p.creador', 'p.fecha',  'IDENTITY(p.nick) AS usuario_id',  'u.nick as usuario_nick', 'c.nombre as categoria_nombre', 'pf.foto as perfil_foto')
        ->from(Posts::class, 'p')
        ->leftJoin('p.categoria', 'c')
        ->leftJoin('p.nick', 'u')
        ->leftJoin('u.perfiles', 'pf')
        ->orderBy('p.fecha', 'DESC')
        ->addOrderBy('p.id', 'DESC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
        
    return $this->render('anuncios/index.html.twig', [
        'results' => $posts ?? [],
        'totalPages' => $totalPages,
        'currentPage' => $page,
    ]);
    }


#[Route('/insertar', name: 'app_insertar', methods: ['GET', 'POST'])]
    public function insertar(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response{

        if (!$session->get('user_authenticated')) {
            return $this->redirectToRoute('app_login');
        }
        
        $post=new Posts();
        $excludedCategories = [7, 8]; 
        $form = $this->createForm(PostsType::class, $post, [
            'excluded_categories' => $excludedCategories,
        ]);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){
            $fechaActual = new \DateTime();
            $post->setFecha($fechaActual);
            $usuarioRepository = $entityManager->getRepository(Usuarios::class);
            $usuario = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
            $post->setNick($usuario);
            $entityManager -> persist($post);
            $entityManager -> flush();

            return $this -> redirectToRoute('app_listar', [], Response::HTTP_SEE_OTHER);

        }
        return $this -> render('anuncios/insertar.html.twig', [
            'form' => $form,
        ]);
    }

}