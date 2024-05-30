<?php


namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Mensajes;
use App\Entity\Usuarios;
use App\Entity\Contactos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\MensajesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class MensajesController extends AbstractController
{
    

    #[Route('/mensajes_av', name: 'app_mensajesav')]
    public function mensajes(EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
        if (!$session->has('user_authenticated') || !$session->get('user_authenticated')) {
            return $this->redirectToRoute('app_login');
        }
       
        $usuarioRepository = $entityManager->getRepository(Usuarios::class);
        $mensajesRepository = $entityManager->getRepository(Mensajes::class);
        $usuarioActual = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
        $nuevoMensaje = new Mensajes();
        $form = $this->createForm(MensajesType::class, $nuevoMensaje);
        $form->handleRequest($request);
    
       $usuariosInteractuados = $mensajesRepository->findUsuariosInteractuados($usuarioActual);

        return $this->render('mensajes/mensajes_av.html.twig', [
            'usuariosInteractuados' => $usuariosInteractuados,
            'form_mensajes' =>$form,
              
        ]);
    }


    #[Route('/mensajes/mostrar/{id}', name: 'app_mostrar_mensaje', methods:['GET', 'POST'])]
    public function mostrar(int $id, EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
       
    if (!$session->has('user_authenticated') || !$session->get('user_authenticated')) {
        return $this->redirectToRoute('app_login');
    }
    $mensajesRepository = $entityManager->getRepository(Mensajes::class);
    $usuarioRepository = $entityManager->getRepository(Usuarios::class);
    $usuario = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);


    if ($usuario) {
    
        $contactoBloqueado = $entityManager->getRepository(Contactos::class)->findOneBy([
            'usuario' => $usuario->getId(), 
            'contacto' => $id,
            'bloqueado' => true
        ]);

        $usuarioBloqueado = $entityManager->getRepository(Contactos::class)->findOneBy([
            'usuario' => $id,
            'contacto' => $usuario->getId(),
            'bloqueado' => true
        ]);
        
        if ($contactoBloqueado || $usuarioBloqueado) {
            return $this->render('usuarios/bloquear_usuario.html.twig', [
                'userId' => $id,
                'haBloqueado' => $contactoBloqueado ? true : false,
                'bloqueadoPorContacto' => $usuarioBloqueado  ? true : false
            ]);
        }
        
    }

    $usuarioEnvia = $usuarioRepository->find($usuario->getId());  
    $usuarioRecibo = $usuarioRepository->find($id);  
    $nuevoMensaje = new Mensajes();
    $form = $this->createForm(MensajesType::class, $nuevoMensaje);
    $form->handleRequest($request);
    $mensajesUsuario = $mensajesRepository->findMensajesUsuario($usuario, $id);    
    if ($form->isSubmitted() && $form->isValid()) {
        
        $nuevoMensaje->setNickenvia($usuarioEnvia);
        $nuevoMensaje->setNickrecibo($usuarioRecibo);
        $fecha = $fechaActual = new \DateTime();
        $nuevoMensaje->setFecha($fechaActual);
        $nuevoMensaje->setLeido(0);
        $entityManager->persist($nuevoMensaje);
        $entityManager->flush();

        $mensajesUsuario = $mensajesRepository->findMensajesUsuario($usuario, $id);    
        $msgHtml = $this->renderView('mensajes/mensaje_list.html.twig', [
        'mensajes_u' => $mensajesUsuario,
        'form_mensajes' => $form->createView(),
        'usuario_actual' => $usuario,
        'usuario_recibo' => $usuarioRecibo,
        ]);
        

    return new JsonResponse(['html' => $msgHtml]);

        
    }

    
    return $this->render('mensajes/mensajes.html.twig', [
        'mensajes_u' => $mensajesUsuario,
        'form_mensajes' =>$form,
        'usuario_actual' => $usuario,
        'usuario_recibo' => $usuarioRecibo,
    ]);
    }



    #[Route('/cargar_conversacion/{usuarioId}', name: 'cargar_conversacion', methods: ['GET', 'POST'])]
    public function cargarConversacion(EntityManagerInterface $entityManager, int $usuarioId, SessionInterface $session,  Request $request): Response
    {
        
        $usuarioRepository = $entityManager->getRepository(Usuarios::class);
        $mensajesRepository = $entityManager->getRepository(Mensajes::class);
        $usuarioActual = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
        $usuarioSeleccionado = $entityManager->getRepository(Usuarios::class)->find($usuarioId);
        
        $conversacion = $mensajesRepository->findConversacion($usuarioActual, $usuarioSeleccionado);
        $mensajesPorFecha = [];
        foreach ($conversacion as $mensaje) {
            $fechaMensaje = $mensaje->getFecha()->format('Y-m-d'); 
        if (!isset($mensajesPorFecha[$fechaMensaje])) {
            $mensajesPorFecha[$fechaMensaje] = [];
        }
        $mensajesPorFecha[$fechaMensaje][] = $mensaje;
            if ($mensaje->getNickrecibo() === $usuarioActual && !$mensaje->isLeido()) {
                $mensaje->setLeido(true);
                $entityManager->persist($mensaje);
            }
        }
        $entityManager->flush();
        
        return $this->render('mensajes/conversacion.html.twig', [
            'conversacion' => $mensajesPorFecha,
            'usuario_seleccionado' => $usuarioSeleccionado->getId(),
            'usuario_actual' => $usuarioActual,
            
        ]);
    }


    #[Route('/enviar_mensaje/{usuarioId}', name: 'enviar_mensaje_ajax', methods: ['POST'])]
    public function enviarMensajeAjax(Request $request, EntityManagerInterface $entityManager, int $usuarioId, SessionInterface $session): JsonResponse
    {
        
        $usuarioRepository = $entityManager->getRepository(Usuarios::class);
        $usuarioActual = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
        $usuarioDestino = $usuarioRepository->find($usuarioId);
    
        $mensaje = new Mensajes();
        $form = $this->createForm(MensajesType::class, $mensaje);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
           
            $mensaje->setNickenvia($usuarioActual);
            $mensaje->setNickrecibo($usuarioDestino);
            $mensaje->setFecha(new \DateTime());
            $mensaje->setLeido(false);
            $entityManager->persist($mensaje);
            $entityManager->flush();
    
            return new JsonResponse(['success' => true]);
        }
    
        return new JsonResponse(['success' => false]);
    }



    #[Route('/actualizar_lista_usuarios', name: 'actualizar_lista_usuarios')]
public function actualizarListaUsuarios(EntityManagerInterface $entityManager, SessionInterface $session): Response
{

    $usuarioRepository = $entityManager->getRepository(Usuarios::class);
    $mensajesRepository = $entityManager->getRepository(Mensajes::class);
    $usuarioActual = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
    $usuariosInteractuados = $mensajesRepository->findUsuariosInteractuados($usuarioActual);
    
    return $this->render('mensajes/lista_usuarios.html.twig', [
        'usuariosInteractuados' => $usuariosInteractuados,
        
    ]);
}

#[Route('/num_mensajes', name: 'num_mensajes')]
    public function numMensajesRecibidos(EntityManagerInterface $entityManager,  SessionInterface $session): Response
    {

        $usuarioRepository = $entityManager->getRepository(Usuarios::class);
        $usuarioActual = $usuarioRepository->findOneBy(['nick' => $session->get('nombre')]);
        $mensajesRepository = $entityManager->getRepository(Mensajes::class);
        $numMensajes = $mensajesRepository->countMensajesNoLeidos($usuarioActual);
            
        return $this->render('num_mensajes.html.twig', [
            'numMensajes' => $numMensajes,
        ]);
    }


}











