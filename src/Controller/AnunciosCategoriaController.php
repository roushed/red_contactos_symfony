<?php

namespace App\Controller;
use App\Entity\Categorias;
use App\Entity\Comentariosp;
use App\Entity\Posts;
use App\Entity\Usuarios;
use App\Entity\Perfiles;
use App\Entity\Imagenes;
use App\Entity\Likes;
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

    private function formatPrice($price)
    {
   
    $formattedPrice = number_format($price, 2, ',', '.');
    $formattedPrice = rtrim($formattedPrice, '0');
    $formattedPrice = rtrim($formattedPrice, ',');

    return $formattedPrice;
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
        
    if ($id === 7 || $id === 8) {

        $anuncios = $entityManager->getRepository(Posts::class)->findAnunciosByCategoria($id);
        $usuario = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);
       
        $likesCounts = [];
        $likesStatus = [];
        foreach ($anuncios as $anuncio) {
            $likesCount = $entityManager->createQueryBuilder()
                ->select('COUNT(l.id)')
                ->from(Likes::class, 'l')
                ->join('l.post', 'p')
                ->where('p.id = :postId')
                ->setParameter('postId', $anuncio['id'])
                ->getQuery()
                ->getSingleScalarResult();
        
            $likesCounts[$anuncio['id']] = $likesCount;
           
        }

        foreach ($anuncios as $index => $anuncio) {

            if (is_array($anuncio)) {
              
                $precioSinFormato = $anuncio['precio'];
                $precioFormateado = $this->formatPrice($precioSinFormato);
                $anuncios[$index]['precio'] = $precioFormateado;
            }
        }
        if ($usuario) {
            foreach ($anuncios as $anuncio) {
                $likeExistente = $entityManager->getRepository(Likes::class)->findOneBy([
                    'post' => $anuncio['id'],
                    'nick' => $usuario->getid() // Asumiendo que el nombre de usuario es el nick en la entidad Likes
                ]);

                $likesStatus[$anuncio['id']] = $likeExistente ? true : false;
            }
           
            
        }else{

            $likesStatus = null;
        }

    }else{

        $anuncios = $entityManager->getRepository(Posts::class)->findAnunciosById($id);
        $likesCounts = null;
        $likesStatus = null;
    }
  
        return $this->render('/anuncios_categoria/anuncios.html.twig', [
            'results' => $anuncios,
            'id_categoria' =>$id,
            'ncategoria' => $categoria->getNombre(),
            'descripcion' => $categoria->getDescripcion(),
            'likescounts' => $likesCounts,
            'likestatus' => $likesStatus,
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
            
                
            if($id == 7 || $id == 8){
                $adquisicion = $request->request->get('tcompra_hidden');
                $municipio = $form->get('ciudad')->getData(); 
                $precio= str_replace('.', '', $post->getPrecio());
                $post->setPrecio($precio);

                if($adquisicion != null){
                    $post->setAdquisicion($adquisicion);
                }
                if($municipio != null){
                    $post->setMunicipio($municipio->getCiudad());
                }
            }

            $fechaActual = new \DateTime();
            $post->setFecha($fechaActual);
            $usuarioRepository = $entityManager->getRepository(Usuarios::class);
            $usuario = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
            $post->setNick($usuario);
            $categoria = $entityManager->getRepository(Categorias::class)->find($id);
            $post->setCategoria($categoria);
            
            

            $entityManager->persist($post);
            $entityManager->flush();
            
            if($id == 7 || $id == 8){
               
                /** @var UploadedFile $imagen */
                $imagenes = $form->get('imagenes')->getData();
                $postId = $post->getId();
                
                if ($imagenes) {
                                      
                    foreach($imagenes as $imagen){

                        $nombreArchivo = md5(uniqid()) . '.' . $imagen->guessExtension();
                        $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
                        $imagen->move(
                            $rutaImagen,
                            $nombreArchivo
                        );
                        $imagenObj = new Imagenes();
                        $imagenObj->setNombre($nombreArchivo);
                        $imagenObj->setPost($entityManager->getRepository(Posts::class)->find($postId));
                        $entityManager->persist($imagenObj);
                        $entityManager->flush();

                        $post->addImagene($imagenObj);

                    }
                    
                }else{
                   
                    $nombreArchivo = ($id == 7) ? 'icono_articulo.png' : (($id == 8) ? 'piso_ico.png' : null);
                    $imagenObj = new Imagenes();
                    $imagenObj->setNombre($nombreArchivo);
                    $imagenObj->setPost($entityManager->getRepository(Posts::class)->find($postId));
                    $entityManager->persist($imagenObj);
                    $entityManager->flush();
                }

            }
            

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
        $precio = $post->getPrecio();
        $precioFormateado = $this->formatPrice($precio);
        $post->setPrecio($precioFormateado);
        $comentarios = $entityManager->getRepository(Comentariosp::class)->getComentarios($id);
        $imagenes = $entityManager->getRepository(Imagenes::class)->findBy(['post' => $id]);

        $likesCount = $entityManager->createQueryBuilder()
                ->select('COUNT(l.id)')
                ->from(Likes::class, 'l')
                ->join('l.post', 'p')
                ->where('p.id = :postId')
                ->setParameter('postId', $id)
                ->getQuery()
                ->getSingleScalarResult();
        
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
            'imagenes' => $imagenes,         
            'likescount' => $likesCount,
        ]);

    }


    #[Route('/buscar-anuncios', name: 'buscar_anuncios')]
    public function buscarAnuncios(Request $request, EntityManagerInterface $entityManager): Response
    {

        $textoBusqueda = $request->query->get('texto');
        $idCategoria = $request->query->get('categoria'); 
        $resultados = $entityManager->getRepository(Posts::class)->findBySubjectAndCategory($textoBusqueda, $idCategoria);
        $resultadosArray = [];
    if($idCategoria != 7 && $idCategoria != 8){
 
        foreach ($resultados as $resultado) {
            $nick = $resultado['nick'];
            $fecha = $resultado['fecha']->format('Y-m-d');
            $foto = $resultado['foto'];
       
            $resultadosArray[] = [
                'id' => $resultado[0]->getId(),
                'subject' => $resultado[0]->getSubject(),
                'nick' => $nick,
                'foto' => $foto,            
                'fecha' => $fecha,
           
            ];
        }


    }else{
     
        foreach ($resultados as $resultado) {
            $nick = $resultado['nick'];
            $fecha = $resultado['fecha']->format('Y-m-d');
            $imagen = $resultado['primera_imagen'];
            $foto = $resultado['foto'];
            $precio = $resultado['precio'];
            $municipio = $resultado['municipio'];
            $adquisicion = $resultado['adquisicion'];

            if (is_numeric($precio)) {
                $precio = $this->formatPrice($precio);
            }
        
            $resultadosArray[] = [
                'id' => $resultado[0]->getId(),
                'subject' => $resultado[0]->getSubject(),
                'nick' => $nick,
                'foto' => $foto,
                'imagen' => $imagen,
                'fecha' => $fecha,
                'precio' => $precio,
                'adquisicion' => $adquisicion,
                'municipio' => $municipio,
                
                
            ];
        }

    }
    
  
  
        return new JsonResponse(['resultados' => $resultadosArray]);
    }


    #[Route('/buscar-adquisicion', name: 'buscar_adquisicion')]
    public function buscarAdquisicion(Request $request, EntityManagerInterface $entityManager): Response
    {

        $adquisicion= $request->query->get('adquisicion');
        $idCategoria = $request->query->get('categoria');
        $resultadosArray = []; 
        $resultados = $entityManager->getRepository(Posts::class)->findByAdquisicionAndCategory($adquisicion, $idCategoria);
                   
        foreach ($resultados as $index => $resultado) {

            $nick = $resultado['nick'];
            $fecha = $resultado['fecha']->format('d F');
            $imagen = $resultado['primera_imagen'];
            $foto = $resultado['foto'];
            $precio = $resultado['precio'];
            $municipio = $resultado['municipio'];
            $adquisicion = $resultado['adquisicion'];
        
            if (is_numeric($precio)) {
                $precio = $this->formatPrice($precio);
            }
        
            $resultadosArray[] = [
                'id' => $resultado[0]->getId(),
                'subject' => $resultado[0]->getSubject(),
                'nick' => $nick,
                'foto' => $foto,
                'imagen' => $imagen,
                'fecha' => $fecha,
                'precio' => $precio,
                'adquisicion' => $adquisicion,
                'municipio' => $municipio,
            ];
        }
    
        return new JsonResponse(['resultados' => $resultadosArray]);
    }



    #[Route('/registrar_like', name: 'registrar_like', methods:['POST'])]
    public function registrarLike(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $postId = $request->request->get('postId');
        
        $post = $entityManager->getRepository(Posts::class)->find($postId);
        $usuario = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);
        
        $likesRepository = $entityManager->getRepository(Likes::class);
        $likeExistente = $likesRepository->findOneBy(['post' => $post, 'nick' => $usuario]);
        
        if ($likeExistente) {

            $entityManager->remove($likeExistente);
            $entityManager->flush();

            return new Response('Like eliminado correctamente', Response::HTTP_OK);

        }else{
            
            $like = new Likes();
            $like->addPost($post);
            $like->addNick($usuario);

            $entityManager->persist($like);
            $entityManager->flush();

            return new Response('Like registrado correctamente', Response::HTTP_OK);
        }

    }
    
}

