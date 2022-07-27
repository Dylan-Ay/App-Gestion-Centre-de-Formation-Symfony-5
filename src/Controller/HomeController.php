<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(SessionRepository $session): Response
    {
        $nextSessions = $session->findNextSessions();
        
        $currentSessions = $session->findCurrentSessions();

        $pastSessions = $session->findPastSessions();

        return $this->render('home/index.html.twig', [
            'nextSessions' => $nextSessions,
            'currentSessions' => $currentSessions,
            'pastSessions' => $pastSessions
        ]);
    }
}
