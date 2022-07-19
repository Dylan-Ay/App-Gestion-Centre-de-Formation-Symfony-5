<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Intern;

class InternController extends AbstractController
{

    /**
     * @Route ("/stagiaires", name= "app_interns")
     */
    public function index (ManagerRegistry $doctrine) : Response
    {
        $interns = $doctrine->getRepository(Intern::class)->findAll();

        return $this->render('intern/index.html.twig', [
            'internsList' => $interns
        ]);
    }
}
