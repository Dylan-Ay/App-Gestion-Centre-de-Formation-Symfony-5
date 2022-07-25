<?php

namespace App\Controller;

use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Session;
use App\Entity\Intern;
use App\Form\SessionType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Repository\SessionRepository;

class SessionController extends AbstractController
{
    // Affiche le detail d'une session
    /**
     * @Route("/session/detailSession/{id}", name="session_detail")
     */
    public function sessionDetail(Session $session, ManagerRegistry $doctrine, SessionRepository $sessionRepository): Response
    {
        $program = $doctrine->getRepository(Program::class)->findBy([
            'session' => $session->getId()
        ]);

        $internsNotInSession = $sessionRepository->findAllNotSubscribed($session->getId());
        
        return $this->render('session/detailSession.html.twig',[
            'session' => $session, // Renvoi l'objet session
            'program' => $program, // Renvoi le programme selon l'ID de la session
            'internsNotInSession' => $internsNotInSession, // Renvoi un tableau de stagiaires non inscrits,
        ]);
    }

    // Ajouter/ Modifier une session
    /**
     * @Route("/session/edit/{id}", name= "edit_session")
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

            return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
        }
        // View pour afficher le formulaire d'ajout
        return $this->render('session/editSession.html.twig', [
            'form' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $session->getId(),
        ]);
    }

    // Désinscrire un stagiaire d'une session

    /**
     * @Route("/session/désinscrire/{idSe}/{idIn}", name = "unsubscribe_intern")
     * @ParamConverter("session", options={"mapping": {"idSe": "id"}})
     * @ParamConverter("intern", options={"mapping": {"idIn": "id"}})
     */
    public function unsubscribeIntern(ManagerRegistry $doctrine, Session $session, Intern $intern) : Response
    {
        $entityManager = $doctrine->getManager();

        $unsubscribedIntern = $intern->getFullName();

        $session->removeIntern($intern);
        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            "Le stagiaire $unsubscribedIntern a bien été désinscrit"
        );

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
    }

    /**
     * @Route("/session/inscrire/{idSe}/{idIn}", name = "subscribe_intern")
     * @ParamConverter("session", options={"mapping": {"idSe": "id"}})
     * @ParamConverter("intern", options={"mapping": {"idIn": "id"}})
     */
    public function subscribeIntern(ManagerRegistry $doctrine, Session $session, Intern $intern) : Response
    {
        $entityManager = $doctrine->getManager();

        $subscribedIntern = $intern->getFullName();

        $session->addIntern($intern);
        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            "Le stagiaire $subscribedIntern a bien été inscrit"
        );

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
    }
}