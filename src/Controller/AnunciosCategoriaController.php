<?php

namespace App\Controller;
use App\Entity\Categorias;
use App\Entity\Comentariosp;
use App\Entity\Posts;
use App\Entity\Usuarios;
use App\Entity\Perfiles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoriasRepository;
use App\Form\PostCategoriaType;
use App\Form\PostCategoriaArticulosType;
use App\Form\ComentariospType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\MimeTypes;


class AnunciosCategoriaController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(CategoriasRepository $categoriasRepository, EntityManagerInterface $entityManager) {
    $this->categoriasRepository = $categoriasRepository;
    $this->entityManager = $entityManager;
    }

    #[Route('/categorias', name: 'app_anuncios_categoria')]
    public function index(EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        $categoriasRepository = $this->entityManager->getRepository(Categorias::class);
        $categorias = $categoriasRepository->findAll();

        return $this->render('anuncios_categoria/index.html.twig', [
            'results' => $categorias,
        ]);
    }

    #[Route('/categorias/{id}', name: 'app_porcategoria')]
    public function mostrar(int $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $categoria = $entityManager->getRepository(Categorias::class)->find($id);
    
        if (!$categoria) {
           
            throw $this->createNotFoundException('La categoría no existe');
        }
        $queryBuilder = $entityManager->createQueryBuilder();
  
        $anuncios = $queryBuilder
        ->select([
            'p.id',
            'p.subject',
            'p.texto',
            'p.imagen',
            'p.fecha',
            'p.precio',
            'IDENTITY(p.nick) AS usuario_id', 
            'u.nick as usuario_nick',
            'pf.foto as perfil_foto',
            '(SELECT COUNT(c.id) FROM App\Entity\Comentariosp c WHERE c.post = p) AS cantidad_comentarios'
        ])
        ->from(Posts::class, 'p')
        ->leftJoin('p.nick', 'u')
        ->leftJoin('u.perfiles', 'pf')
        ->where('p.categoria = :categoria')
        ->setParameter('categoria', $id)
        ->orderBy('p.fecha', 'DESC')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY); 
     

        return $this->render('/anuncios_categoria/anuncios.html.twig', [
            'results' => $anuncios,
            'id_categoria' =>$id,
            'ncategoria' => $categoria->getNombre(),
            'descripcion' => $categoria->getDescripcion(),
        ]);
    }

    #[Route('/categorias/insertar/{id}', name: 'app_porcategoria_insertar', methods: ['GET', 'POST'])]
    public function insertar(int $id, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response{

        

        $post=new Posts();
        if($id != 7 && $id != 8 ){
            $form=$this -> createForm(PostCategoriaType::class, $post);
        }else{
            $form=$this -> createForm(PostCategoriaArticulosType::class, $post);
        }

        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){

            $fechaActual = new \DateTime();
            $post->setFecha($fechaActual);
            $usuarioRepository = $entityManager->getRepository(Usuarios::class);
            $usuario = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
            $post->setNick($usuario);
            $categoria = $entityManager->getRepository(Categorias::class)->find($id);
            $post->setCategoria($categoria);
            
            if($id == 7 || $id == 8){
               
                /** @var UploadedFile $imagen */
                $imagen = $form->get('imagen')->getData();
            
                if ($imagen) {
                    $nombreArchivo = md5(uniqid()) . '.' . $imagen->guessExtension();
                    $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
                    $imagen->move(
                        $rutaImagen,
                        $nombreArchivo
                    );
                    $post->setImagen($nombreArchivo);
                }else{
                    $nombreArchivo ='icono_articulo.png';
                    $post->setImagen($nombreArchivo);
                }

                
            }
            $entityManager -> persist($post);
            $entityManager -> flush();

            return $this -> redirectToRoute('app_porcategoria', ['id' => $id], Response::HTTP_SEE_OTHER);

        }
        return $this -> render('anuncios_categoria/insertar.html.twig', [
            'form' => $form,
            'id_categoria' => $id,
            
        ]);
    }

    #[Route('/categorias/ver/{id}', name:'app_porcategoria_ver', methods:['GET', 'POST'])]

    public function ver(int $id,Request $request, EntityManagerInterface $entityManager,  SessionInterface $session): Response{
        
        $post = $entityManager->getRepository(Posts::class)->find($id);
        $perfil = $entityManager->getRepository(Perfiles::class)->findOneBy(['nick' => $post->getNick()]);
        
        $nickId = $post->getNick()->getId();
        $nick = $entityManager->getRepository(Usuarios::class)->find($nickId)->getNick();
        $comentarios = $entityManager->getRepository(Comentariosp::class)->getComentarios($id);
       
        $nuevoComentario = new Comentariosp();
        $form = $this->createForm(ComentariospType::class, $nuevoComentario);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            $mynick = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);
            $nuevoComentario->setNick($mynick);
            $nuevoComentario->setPost($post);
            $entityManager->persist($nuevoComentario);
            $entityManager->flush();
            $comentarios = $entityManager->getRepository(Comentariosp::class)->getComentarios($id);
           
            $comentariosHtml = $this->renderView('anuncios_categoria/comentarios.html.twig', ['comentarios' => $comentarios]);
            return new JsonResponse(['comentarios' => $comentariosHtml]);
        }
       
        return $this -> render('anuncios_categoria/ver.html.twig',[
            'id_nick' => $post->getNick()->getId(),
            'nick' => $nick,
            'post' => $post,
            'comentarios' => $comentarios,
            'form_comentarios' =>$form,
            'perfil' => $perfil,          

        ]);

    }


    #[Route('/buscar-anuncios', name: 'buscar_anuncios')]
    public function buscarAnuncios(Request $request, EntityManagerInterface $entityManager): Response
    {

        $textoBusqueda = $request->query->get('texto');
        $idCategoria = $request->query->get('categoria'); 
        $resultados = $entityManager->getRepository(Posts::class)->findBySubjectAndCategory($textoBusqueda, $idCategoria);
    
        $resultadosArray = [];
    foreach ($resultados as $resultado) {
        $nick = $resultado['nick'];
        $fecha = $resultado['fecha']->format('Y-m-d');
        $foto = $resultado['foto'];
        $imagen = $resultado['imagen'];
        $precio = $resultado['precio'];
    
        $resultadosArray[] = [
            'id' => $resultado[0]->getId(),
            'subject' => $resultado[0]->getSubject(),
            'nick' => $nick,
            'fecha' => $fecha,
            'foto' => $foto,
            'imagen' => $imagen,
            'precio' => $precio,
            
        ];
    }
  
        return new JsonResponse(['resultados' => $resultadosArray]);
    }

    
}

