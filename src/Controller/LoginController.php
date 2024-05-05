<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\LoginType;
use App\Form\RegistrosType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Usuarios;
use App\Repository\PostsRepository;
use App\Entity\Perfiles;
use App\Form\PerfilesType;
use App\Entity\PerfilAficiones;
use Symfony\Component\Form\FormError;


class LoginController extends AbstractController
{

    function __construct(PostsRepository $postsRepository)
    {
        $this->postsRepository = $postsRepository;

    }
    #[Route('/login', name: 'app_login')]
    public function login(Request $request,  SessionInterface $session, EntityManagerInterface $entityManager)
    {
       
        if ($session->get('user_authenticated')) {
            return $this->redirectToRoute('app_listar');
        }

        $usuarioRepository = $entityManager->getRepository(Usuarios::class);
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            
            $credentials = $form->getData();
            $usuario = $usuarioRepository->findOneBy(['nick' => $credentials->getNick()]);

            if ($usuarioRepository->isCredentialsValid($credentials->getNick(), $credentials->getPassword(), $entityManager)) {
         
                $u_img = $usuario->getPerfil()->getFoto();
                $session->set('user_authenticated', true); 
                $session->set('nombre', $credentials->getNick());
                $session->set('imagen', $u_img);
                return $this->redirectToRoute('app_listar');
            } else {
                $this->addFlash('error', 'Nick o Password incorrecto');
            }
        }

        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout( SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $session->get('nombre')]);

        if ($user !== null) {

            $user->setOnline(false);
            $fechaActual = new \DateTime();
            $user->setFecha($fechaActual);
            $entityManager->flush();     
        } 

        $session->remove('user_authenticated');
        $session->remove('nombre');
        $session->remove('imagen');
        return $this->redirectToRoute('app_login');
    }



    #[Route('/registro', name: 'app_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, EntityManagerInterface $entityManager): Response{

        
        $usuario=new Usuarios();
        $form=$this -> createForm(RegistrosType::class, $usuario);
        $form -> handleRequest($request);
        $perfil = $usuario->getPerfil(); 
       
        if($form -> isSubmitted() && $form -> isValid()){

            $fechaActual = new \DateTime();
            $nick = $form->get('nick')->getData();
            $password = $form->get('password')->getData(); 
            $email = $form->get('perfil')->get('email')->getData();
            $ciudadSeleccionada = $form->get('perfil')->get('ciudad')->getData();
            $imagen = $form['perfil']['foto']->getData(); 
            $aficionesSeleccionadas = $form->get('perfil')->get('aficiones')->getData();
        
            $existingUser = $entityManager->getRepository(Usuarios::class)->findOneBy(['nick' => $nick]);
            $existingEmail = $entityManager->getRepository(Perfiles::class)->findOneBy(['email' => $email]);

            if ($existingUser) {
              
                $form->get('nick')->addError(new FormError('El nick ya está registrado.'));
            }else if($existingEmail){

                $form->get('perfil')->get('email')->addError(new FormError('El email ya se encuentra registrado.'));
            }else{
                
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $usuario->setOnline(false);
                $usuario->setFecha($fechaActual);
                $usuario->setPassword($hashedPassword);
                $entityManager -> persist($usuario);
                $entityManager -> flush();

            if (!empty($aficionesSeleccionadas)) {
                foreach ($aficionesSeleccionadas as $aficionSeleccionada) {
                  
                    $perfilAficion = new PerfilAficiones();
            
                   
                    $perfilAficion->setPerfil($perfil);
                    $perfilAficion->setAficion($aficionSeleccionada);
            
                 
                    $entityManager->persist($perfilAficion);
                }
            }

          
            $idUsuario = $usuario->getId();
            $idUsuario = $entityManager->getRepository(Usuarios::class)->find($idUsuario);
            
            $perfil->setNick($idUsuario);
            $perfil->setCiudad($ciudadSeleccionada->getCiudad());
            $perfil->setEmail($email);
            
            
            if ($imagen) {
              
                $nombreArchivo = md5(uniqid()) . '.' . $imagen->guessExtension();

                $rutaImagen = $this->getParameter('kernel.project_dir') . '/public/image';
                    
                $imagen->move(
                    $rutaImagen,
                    $nombreArchivo
                );

                $perfil->setFoto($nombreArchivo);
            }else{

                $nombreArchivo ='img_defecto.jpg';
                $perfil->setFoto($nombreArchivo);
            }

            $entityManager->persist($perfil);
            $entityManager->flush();

            return $this -> redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
                
            }
           
        }
        return $this -> render('login/registro.html.twig', [
            'form' => $form,
        ]);
    }


}
