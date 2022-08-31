<?php

namespace App\Controller;

use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Session;
use App\Entity\Intern;
use App\Entity\Module;
use App\Form\SessionType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Repository\SessionRepository;

class SessionController extends AbstractController
{
    // <-------- Affiche le detail d'une session et permet d'ajouté un module -------->

    /**
     * @Route("/session/detailSession/{id}", name="session_detail")
     */
    public function sessionDetail(Session $session, ManagerRegistry $doctrine, SessionRepository $sessionRepository, Request $request): Response
    {
        $programsList = $doctrine->getRepository(Program::class)->findBy([
            'session' => $session->getId()
        ]);

        // Affiche les stagiaires non inscrits à la session
        $internsNotInSession = $sessionRepository->findAllNotSubscribed($session->getId());

        return $this->render('session/detailSession.html.twig',[
            'session' => $session, // Renvoi l'objet session
            'programsList' => $programsList, // Renvoi le programme selon l'ID de la session
            'internsNotInSession' => $internsNotInSession
        ]);
    }


    // <-------- Ajouter une session ou modifier ses informations -------->

    /**
     * @Route("/session/add", name= "add_session")
     * @Route("/session/edit/{id}", name= "edit_session")
     */
    public function editSession(ManagerRegistry $doctrine, Session $session = null, Request $request) : Response
    {
        if (!$session){
            $session = new Session();
        }
            
        // Form modification de la session
        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request); 
        
        // Vérifie que le formulaire a été soumit et que les champs sont valides (similiaire à filter_input)
        if ($form->isSubmitted() && $form->isValid()) {

            // if($session->getPrograms())
            // {
            //     // si l'id du module existe pas dans le programme de la session
            // }

            $session = $form->getData(); //Permet d'hydrater l'objet session
            
            $sessionManager = $doctrine->getManager(); // Récupère le manager
            $sessionManager->persist($session); // Prepare les données
            $sessionManager->flush(); // Execute la request (insert into)

            $this->addFlash(
                'notice-success',
                "La session a bien été mise à jour"
            );

            return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
        }

        // View pour afficher le formulaire d'ajout
        return $this->render('session/editSession.html.twig', [
            'form' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $session->getId(),
            'sessionId' => $session->getId()
        ]);
    }

    // <-------- Supprimer une session -------->

    /**
     * @Route("/session/delete/{id}", name="delete_session")
     */
    public function deleteSession(ManagerRegistry $doctrine, Session $session)
    {
        $entityManager = $doctrine->getManager();

        $sessionDeleted = $session->getFormation()->getTitle();

        $date = $session->getDateStart();

        $sessionDate = $date->format('d-m-Y');

        $entityManager->remove($session);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            "La session $sessionDeleted du $sessionDate a bien été supprimée"
        );

        return $this->redirectToRoute('app_formation');
    }


    // <-------- Désinscrire un stagiaire d'une session -------->

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
            'notice-success',
            "Le stagiaire $unsubscribedIntern a bien été désinscrit"
        );

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
    }


    // <-------- Inscrire un stagiaire à une session pour la view detailSession -------->

    /**
     * @Route("/session/inscrire/{idSe}/{idIn}", name = "subscribe_intern")
     * @ParamConverter("session", options={"mapping": {"idSe": "id"}})
     * @ParamConverter("intern", options={"mapping": {"idIn": "id"}})
     */
    public function subscribeIntern(ManagerRegistry $doctrine, Session $session, Intern $intern) : Response
    {
        if ($session->leftPlacesNumber() === 0) {
            $this->addFlash(
                'notice-error',
                "Il n'y a plus de place disponibles pour cette session"
            );
            
        }else{
            $entityManager = $doctrine->getManager();
    
            $subscribedIntern = $intern->getFullName();
    
            $session->addIntern($intern);
            $entityManager->persist($session);
            $entityManager->flush();
    
            $this->addFlash(
                'notice-success',
                "Le stagiaire $subscribedIntern a bien été inscrit"
            );
        }

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
    }


    // <-------- Suppression d'un module du programme pour la view detailSession -------->

    /**
     * @Route("session/supression-module/{idSe}/{idPro}/{idMo}", name="delete_program_module")
     * @ParamConverter("session", options={"mapping": {"idSe": "id"}})
     * @ParamConverter("program", options={"mapping": {"idPro": "id"}})
     * @ParamConverter("module", options={"mapping": {"idMo": "id"}})
     */
    public function deleteProgramModule(ManagerRegistry $doctrine, Session $session, Program $program, Module $module) : Response
    {
        $entityManager = $doctrine->getManager();

        $removedModule = $program->getModule()->getTitle();

        $session->removeProgram($program);
        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash(
            'module-deleted',
            "Le module $removedModule a bien été supprimé"
        );

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);

    }
}