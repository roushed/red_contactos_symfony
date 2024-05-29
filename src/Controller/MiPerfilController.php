<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Usuarios;
use App\Entity\Aficiones;
use App\Entity\Perfiles;
use App\Entity\Posts;
use App\Entity\Actividades;
use App\Entity\Municipios;
use App\Entity\PerfilAficiones;
use App\Entity\Imagenes;
use App\Entity\Galerias;
use DateTime;

class MiPerfilController extends AbstractController
{
    #[Route('/mi_perfil', name: 'app_mi_perfil')]
    public function index(EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        
        $usuario = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);
        $posts = $entityManager->getRepository(Posts::class)->findBy(['nick' => $usuario]);
        $municipios = $entityManager->getRepository(Municipios::class)->findAll();
        $perfil = $entityManager->getRepository(Perfiles::class)->findOneBy(['nick' => $usuario]);
        $galeriaImagenes = $entityManager->getRepository(Galerias::class)->findBy(['perfil'=>$perfil->getId()]);
        
        $datosUsuario = [
            'id' => $usuario->getPerfil()->getId(),
            'nombre' => $usuario->getPerfil()->getNombre(),
            'apellidos' => $usuario->getPerfil()->getApellidos(),
            'password' => $usuario->getPassword(),
            'email' => $usuario->getPerfil()->getEmail(),
            'edad' => $usuario->getPerfil()->getEdad()->format('Y-m-d'),
            'ciudad' => $usuario->getPerfil()->getCiudad(),
            'foto' => $usuario->getPerfil()->getFoto(),
            'descripcion' => $usuario->getPerfil()->getDescripcion(),
            // Otros datos del usuario...
        ];

        $listaAficiones = $entityManager->getRepository(Aficiones::class)->findAll();
        $queryBuilder = $entityManager->createQueryBuilder();
        $aficionesUsuario = $queryBuilder
            ->select('af.id, af.nombre')
            ->from('App\Entity\Aficiones', 'af')
            ->join('af.perfilAficiones', 'pa')
            ->join('pa.perfil', 'p')
            ->where('p.nick = :idUsuario')
            ->setParameter('idUsuario', $usuario->getId())
            ->getQuery()
            ->getResult();

            $actividadesUsuario = $entityManager->getRepository(Actividades::class)
            ->createQueryBuilder('a')
            ->join('a.actividadesUsuarios', 'au')
            ->where('au.nick = :usuario')
            ->andWhere('au.creador = true') 
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getResult();

            

        return $this->render('mi_perfil/index.html.twig', [
            'resultados' => $datosUsuario,
            'listaAficiones' =>$listaAficiones,
            'aficionesUsuario' => $aficionesUsuario,
            'posts' => $posts,
            'actividades' => $actividadesUsuario,
            'municipios' => $municipios,
            'imagenes' =>$galeriaImagenes,
        ]);
        }


        #[Route('/editar_perfil', name: 'editar_perfil')]
        public function guardarDatos(EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
        {
           
            $usuario = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);
            $perfil = $entityManager->getRepository(Perfiles::class)->findOneBy(['nick' => $usuario]);
            $perfilAficiones = $entityManager->getRepository(PerfilAficiones::class)->findBy(['perfil'=>$perfil->getId()]);
            $aficionesActuales = [];
            $nombre = $request->request->get('nombre');
            $apellidos = $request->request->get('apellidos');
            $email = $request->request->get('email');
            $idciudad = $request->request->get('ciudad');
            $municipio = $entityManager->getRepository(Municipios::class)->find($idciudad);
            $descripcion = $request->request->get('descripcion');
            $aficionesSelect = $request->get('aficiones');
            $edad=$request->request->get('edad');
            $edad = DateTime::createFromFormat('Y-m-d', $edad);
            $password=$request->get('password');
         

            if ($usuario->getPassword() !== $password) {
               
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $usuario->setPassword($hashedPassword);
            }
           
            $aficionesActuales = [];
            foreach ($perfilAficiones as $perfilAficion) {
                $aficionesActuales[] = $perfilAficion->getAficion()->getId();
            }
            if ($aficionesActuales != $aficionesSelect) {
                
                foreach ($perfilAficiones as $perfilAficion) {
                    $entityManager->remove($perfilAficion);
                }
                $entityManager->flush();
    
                foreach ($aficionesSelect as $aficionId) {
                    
                    $aficion = $entityManager->getRepository(Aficiones::class)->findOneBy(['id' => $aficionId]);
                
                    $perfilAficion = new PerfilAficiones();
                    $perfilAficion->setPerfil($perfil);
                    $perfilAficion->setAficion($aficion);
                
                    $entityManager->persist($perfilAficion);
                }
                
                $entityManager->flush();
                
            }
            
            $perfil->setNombre($nombre);
            $perfil->setApellidos($apellidos);
            $perfil->setEmail($email);
            $perfil->setCiudad($municipio->getCiudad());
            $perfil->setDescripcion($descripcion);
            $perfil->setEdad($edad);

            $imagen = $request->files->get('imagen');
        
            if ($imagen) {

                $nombreArchivo = md5(uniqid()) . '.' . $imagen->guessExtension();
                $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
                $imagen->move($rutaImagen, $nombreArchivo);
                $session->set('imagen', $nombreArchivo);
                $perfil->setFoto($nombreArchivo);
            }
         
            $entityManager->flush();

            return $this->redirectToRoute('app_mi_perfil', ['edit_success' => true]);

        }

        #[Route('/editar/post/{id}', name: 'editar_post')]
        public function editarPost(Request $request, int $id, EntityManagerInterface $entityManager): Response
        {
         
       
            $post = $entityManager->getRepository(Posts::class)->find($id);
            $municipios = $entityManager->getRepository(Municipios::class)->findAll();
            $imagenes = $entityManager->getRepository(Imagenes::class)->findBy(['post' => $post]);
          
         
            if (!$post) {
                throw $this->createNotFoundException('No se encontró el post con el ID: ' . $id);
            }

            if ($request->isMethod('POST')) {
                
                $subject = $request->request->get('subject');
                $texto = $request->request->get('texto');
        
                $post->setSubject($subject);
                $post->setTexto($texto);

                if ($post->getCategoria()->getId() == 7 || $post->getCategoria()->getId() == 8) {
             
                    $precio= str_replace('.', '', $request->request->get('precio'));
                    $telefono = $request->request->get('telefono');
                    $municipioId = $request->request->get('municipio');
                    $municipio = $entityManager->getRepository(Municipios::class)->find($municipioId);
                    
                    $post->setSubject($subject);
                    $post->setTexto($texto);
                    $post->setPrecio($precio);
                    $post->setTelefono($telefono);
                    $post->setMunicipio($municipio ? $municipio->getCiudad() : null);
                    
                    if ($post->getCategoria()->getId() == 8) {
                        $adquisicion = $request->request->get('tcompra');
                        $post->setAdquisicion($adquisicion);
                    }

                }
        
                $entityManager->flush();
        
                return $this->redirectToRoute('app_mi_perfil');
            }
           
            return $this->render('mi_perfil/editar_post.html.twig', [
                'post' => $post,
                'municipios' => $municipios,
                'imagenes' => $imagenes,
            ]);
        }






        #[Route('/editar/actividad/{id}', name: 'editar_actividad')]
        public function editarActividad(Request $request, int $id, EntityManagerInterface $entityManager): Response
        {
       
            $actividad = $entityManager->getRepository(Actividades::class)->find($id);
            $usuarioRepository = $entityManager->getRepository(Usuarios::class);
            $usuarios = $usuarioRepository->findUsuariosByActividad($id);
            $municipios = $entityManager->getRepository(Municipios::class)->findAll();

          
            if (!$actividad) {
                throw $this->createNotFoundException('No se encontró la actividad con el ID: ' . $id);
            }

            if ($request->isMethod('POST')) {

                $nombre = $request->request->get('nombre');
                $descripcion = $request->request->get('descripcion');
                $cantidad = $request->request->get('cantidad');
                $fecha = $request->request->get('fecha');
                $hora = $request->request->get('hora');
                $direccion = $request->request->get('direccion');
                $idmunicipio= $request->request->get('municipio');
                $municipio = $entityManager->getRepository(Municipios::class)->find($idmunicipio);
                $horaDateTime = \DateTime::createFromFormat('H:i', $hora);
                $actividad->setNombre($nombre);
                $actividad->setDescripcion($descripcion);
                $actividad->setNpersonas($cantidad);
                $actividad->setFecha(new \DateTime($fecha));
                $actividad->setHora($horaDateTime);
                $actividad->setDireccion($direccion);
                $actividad->setMunicipio($municipio->getCiudad());

                $imagen = $request->files->get('imagen');
              
                if ($imagen) {

                    $nombreArchivo = md5(uniqid()) . '.' . $imagen->guessExtension();
                    $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
                    $imagen->move($rutaImagen, $nombreArchivo);
                    $actividad->setImg($nombreArchivo);
                }
                
                $entityManager->flush();

                return $this->redirectToRoute('app_mi_perfil');
            }
               
            return $this->render('mi_perfil/editar_actividad.html.twig', [
                'actividad' => $actividad,
                'usuarios' =>$usuarios,
                'municipios' => $municipios,
            ]);
        }

        #[Route('/guardar-imagenes', name: 'guardar_imagenes', methods: ['POST'])]
        public function guardarImagenes(Request $request, EntityManagerInterface $entityManager): Response
        {

            $files = $request->files->get('images');
            $postId = $request->request->get('post_id');
            $post = $entityManager->getRepository(Posts::class)->find($postId);
            $imagenes = [];

            foreach ($files as $file) {
                $imagen = new Imagenes();
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';

                $file->move($rutaImagen, $filename);
                $imagen->setNombre($filename);
                $imagen->setPost($post);
                $entityManager->persist($imagen);
                $imagenes[] = $imagen;
            }

            $entityManager->flush();

            $imagenesRepository = $entityManager->getRepository(Imagenes::class);
            $allImages = $imagenesRepository->findByPostId($postId);

         
            $imagesData = [];
            foreach ($allImages as $imagen) {
                $imagesData[] = [
                    'id' => $imagen->getId(),
                    'nombre' => $imagen->getNombre(),
                ];
            }

            return new JsonResponse($imagesData);


        }

        #[Route('/guardar-galerias', name: 'guardar_galerias', methods: ['POST'])]
        public function guardarGalerias(Request $request, EntityManagerInterface $entityManager): Response
            {
 
                $files = $request->files->get('images');
                $perfilId = $request->request->get('perfil_id');
                $perfil = $entityManager->getRepository(Perfiles::class)->find($perfilId);
               
                $imagenes = [];
    
                foreach ($files as $file) {
                    $galeria = new Galerias();
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
    
                    $file->move($rutaImagen, $filename);
                    $galeria->setNombre($filename);
                    $galeria->setPerfil($perfil);
                    $entityManager->persist($galeria);
                    $imagenes[] = $galeria;
                }

                $entityManager->flush();
                $galeriasRepository = $entityManager->getRepository(Galerias::class);
                $allImages = $galeriasRepository->findByPerfilId($perfilId);

                $imagesData = [];
                foreach ($allImages as $imagen) {
                    $imagesData[] = [
                        'id' => $imagen->getId(),
                        'nombre' => $imagen->getNombre(),
                    ];
                }

                return $this->json($imagesData);
}

        #[Route('/eliminar-imagen/{id}', name: 'eliminar_imagen', methods: ['DELETE'])]
        public function eliminarImagen(int $id, Request $request, EntityManagerInterface $entityManager): Response
        {

            $imagen = $entityManager->getRepository(Imagenes::class)->find($id);
    
            if (!$imagen) {
                return new Response('La imagen no se encontró', Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($imagen);
            $entityManager->flush();

            return new Response('La imagen se eliminó correctamente', Response::HTTP_OK);
        }

        #[Route('/eliminar-post/{id}', name: 'eliminar_post', methods: ['DELETE'])]
        public function eliminarPost(int $id, EntityManagerInterface $entityManager): JsonResponse
        {
            $post = $entityManager->getRepository(Posts::class)->find($id);
    
            
            if (!$post) {
                return new JsonResponse(['message' => 'El post no se encontró'], Response::HTTP_NOT_FOUND);
            }
        
            $entityManager->remove($post);
            $entityManager->flush();
        
            return new JsonResponse(['message' => 'El post se eliminó correctamente'], Response::HTTP_OK);
        }


        #[Route('/eliminar-actividad/{id}', name: 'eliminar_actividad', methods: ['DELETE'])]
        public function eliminarActividad(int $id, EntityManagerInterface $entityManager): JsonResponse
        {
            $actividad = $entityManager->getRepository(Actividades::class)->find($id);
        
            if (!$actividad) {
                return new JsonResponse(['message' => 'La actividad no se encontró'], Response::HTTP_NOT_FOUND);
            }

            $actividadesUsuarios = $actividad->getActividadesUsuarios();
            
            foreach ($actividadesUsuarios as $actividadUsuario) {
                $entityManager->remove($actividadUsuario);
            }

            $entityManager->remove($actividad);
            $entityManager->flush();

            return new JsonResponse(['message' => 'La actividad se eliminó correctamente'], Response::HTTP_OK);
        }


        #[Route('/eliminar-galeria/{id}', name: 'eliminar_galeria', methods: ['DELETE'])]
        public function eliminarGaleria(int $id, Request $request, EntityManagerInterface $entityManager): Response
        {
         
            $galeria = $entityManager->getRepository(Galerias::class)->find($id);
    
          
            if (!$galeria) {
                return new Response('La galería no se encontró', Response::HTTP_NOT_FOUND);
            }
          
            $entityManager->remove($galeria);
            $entityManager->flush();

            return new Response('La galeria se eliminó correctamente', Response::HTTP_OK);
        }





        #[Route('/eliminar_usuario', name: 'eliminar_usuario', methods: ['POST'])]
            public function eliminarUsuario(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
            {
                $usuario = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);

                if ($usuario) {
                    $entityManager->remove($usuario);
                    $entityManager->flush();

                    $session->remove('user_authenticated');
                    $session->remove('nombre');
                    $session->remove('imagen');
                    $session->invalidate();

                    return $this->redirectToRoute('app_login');
                }

                return new Response('Usuario no encontrado', 404);
            }


}


