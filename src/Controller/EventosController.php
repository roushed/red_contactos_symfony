<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Eventos;

class EventosController extends AbstractController
{
    #[Route('/eventos', name: 'app_eventos')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        try {
       
            $eventosRepository = $entityManager->getRepository(Eventos::class);
            $eventos = $eventosRepository->createQueryBuilder('e')
                ->orderBy('e.fecha', 'DESC')
                ->getQuery()
                ->getResult();
        
            return $this->render('eventos/index.html.twig', [
                'eventos' => $eventos,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Se produjo un error al listar los eventos: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
