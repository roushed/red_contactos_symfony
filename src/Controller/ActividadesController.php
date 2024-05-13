<?php

namespace App\Controller;
use App\Entity\Comentariosa;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Actividades;
use App\Entity\Usuarios;
use App\Entity\ActividadesUsuarios; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\ComentariosaType;
use App\Form\ActividadesType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActividadesController extends AbstractController
{
    #[Route('/actividades', name: 'app_actividades')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $actividadesRepository = $entityManager->getRepository(Actividades::class);
     
        $actividades = $actividadesRepository->findActividades();
        
        return $this->render('actividades/index.html.twig', [
            'results' => $actividades,
        ]);
    }


    #[Route('/actividades/ver/{id}', name:'app_actividades_ver', methods:['GET', 'POST'])]

    public function ver(int $id,Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response{

        $authenticated = $session->get('user_authenticated');
        $usuarioRepository = $entityManager->getRepository(Usuarios::class);
        
        if ($authenticated) {
            $usuario = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
            $usuarioActual = $entityManager->getRepository(Usuarios::class)->find($usuario->getId());
            $actividadRepository = $entityManager->getRepository(ActividadesUsuarios::class);
            $inscrito = $actividadRepository->findInscrito($id, $usuarioActual);
        } else {
            $inscrito = null; 
        }
    
        $action = $request->query->get('action');
    
        switch ($action) {

            case 'inscribir':
                
                $usuarioActual = $usuarioRepository->find($usuario->getId());
                $actividadesUsuarios = new ActividadesUsuarios();
                $actividad = $entityManager->getRepository(Actividades::class)->find($id);
                $actividadesUsuarios->setIdActividad($actividad);
                $actividadesUsuarios->setNick($usuarioActual);
                $entityManager->persist($actividadesUsuarios);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_actividades_ver', ['id' => $id]);
    
            case 'desinscribir':
                
                $usuarioActual = $usuarioRepository->find($usuario->getId());
                $actividadRepository = $entityManager->getRepository(ActividadesUsuarios::class);
                $inscripcion = $actividadRepository->findOneBy([
                    'id_actividad' => $id,
                    'nick' => $usuarioActual,
                ]);
                if ($inscripcion) {
                    $entityManager->remove($inscripcion);
                    $entityManager->flush();
                    return $this->redirectToRoute('app_actividades_ver', ['id' => $id]);
                }
                break;
        }
    
        $actividadRepository = $entityManager->getRepository(Actividades::class);
        $actividad = $actividadRepository->findActividad($id);
        $usuarios = $usuarioRepository->findUsuariosByActividad($id);
    
            $actividadc = $entityManager->getRepository(Actividades::class)->find($id);
            $comentarios = $entityManager->getRepository(Comentariosa::class)->getComentariosActividad($id);
           
            $nuevoComentario = new Comentariosa();
            $form = $this->createForm(ComentariosaType::class, $nuevoComentario);
            $form->handleRequest($request);
    
            if ($form->isSubmitted()) {
                $mynick = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);
                $nuevoComentario->setNick($mynick);
                $nuevoComentario->setActividad($actividadc);
                $entityManager->persist($nuevoComentario);
                $entityManager->flush();

                $comentarios = $entityManager->getRepository(Comentariosa::class)->getComentariosActividad($id);
                $comentariosHtml = $this->renderView('actividades/comentariosa.html.twig', ['comentarios' => $comentarios]);
                return new JsonResponse(['comentarios' => $comentariosHtml]);
    
           
                return $this->redirectToRoute('app_actividades_ver', ['id' => $actividadc->getId()]);

            }
    
        return $this->render('actividades/ver.html.twig', [
            'results' => $actividad,
            'results_u' => $usuarios,
            'inscrito' => $inscrito,
            'comentarios' => $comentarios,
            'form_comentarios' =>$form,
        ]);

   
}


#[Route('/actividades/insertar', name: 'app_actividades_insertar', methods: ['GET', 'POST'])]
    public function insertar(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response{

        
        
        $actividad=new Actividades();
        $form=$this -> createForm(ActividadesType::class, $actividad);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid()){

            $municipioSeleccionado = $form->get('municipio')->getData()->getCiudad();
            $actividad->setMunicipio($municipioSeleccionado);

            $entityManager -> persist($actividad);
            $entityManager -> flush();

            /** @var UploadedFile $imagen */
            $imagen = $form->get('imagen')->getData();
       
            if ($imagen) {
             
                $nombreArchivo = md5(uniqid()) . '.' . $imagen->guessExtension();
                $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
                   
                $imagen->move(
                    $rutaImagen,
                    $nombreArchivo
                );
               $actividad->setImg($nombreArchivo);

            }else{
                $nombreArchivo ='/icos/icono_plan.jpg';
                $actividad->setImg($nombreArchivo);
            }
            
            $usuarioRepository = $entityManager->getRepository(Usuarios::class);
            $usuario = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
           
            $actividadUsuario = new ActividadesUsuarios();
            $actividadUsuario->setIdActividad($actividad);
            $actividadUsuario->setNick($usuario);
            $actividadUsuario->setCreador(true);

            $entityManager->persist($actividadUsuario);
            $entityManager->flush();

            return $this -> redirectToRoute('app_actividades');

        }
        return $this -> render('actividades/insertar.html.twig', [
            'form' => $form,
            
        ]);
    }

}