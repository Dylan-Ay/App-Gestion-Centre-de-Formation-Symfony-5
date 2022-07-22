<?php

namespace App\Controller;

use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Session;

class SessionController extends AbstractController
{
    // Affiche le detail d'une session
    /**
     * @Route("/session/detailSession/{id}", name="session_detail")
     */
    public function sessionDetail(Session $session, ManagerRegistry $doctrine): Response
    {
        $program = $doctrine->getRepository(Program::class)->findBy([
            'session' => $session->getId()
        ]);
        
        return $this->render('session/detail.html.twig',[
            'session' => $session,
            'program' => $program
        ]);
    }
}
