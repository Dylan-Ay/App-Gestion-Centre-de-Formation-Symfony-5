<?php

namespace App\Controller;

use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Session;
use App\Form\SessionType;
use Symfony\Component\HttpFoundation\Request;

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
            'session' => $session, // Renvoi l'objet session
            'program' => $program // Renvoi le programme selon l'ID de la session
        ]);
    }

    // Ajouter/ Modifier la session
    /**
     * @Route("session/edit/{id}", name= "edit_session")
     */
    public function editSession(ManagerRegistry $doctrine, Session $session = null, Request $request) : Response
    {
        if (!$session){
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);
        
        $form->handleRequest($request); 
        
        // Vérifie que le formulaire a été soumit et que les champs sont valides (similiaire à filter_input)
        if ($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData(); //Permet d'hydrater l'objet employe
            
            $sessionManager = $doctrine->getManager(); // Récupère le manager
            $sessionManager->persist($session); // Prepare les données
            $sessionManager->flush(); // Execute la request (insert into)

            $this->addFlash(
                'notice',
                "La session a bien été mise à jour"
            );

            return $this->redirectToRoute('detail', ['id' => $session->getId()]);
        }
        // View pour afficher le formulaire d'ajout
        return $this->render('session/editSession.html.twig', [
            'form' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $session->getId()
        ]);
    }
}
