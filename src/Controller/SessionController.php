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
use App\Repository\ModuleRepository;
use App\Form\ProgramType;

class SessionController extends AbstractController
{
    // <-------- Affiche le detail d'une session -------->

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

        $formProgram = $this->createForm(ProgramType::class);

        // Défini la session en cours par défaut
        $formProgram->get('session')->setData($session); 
        
        // On récupère les informations du form
        $formProgram->handleRequest($request);


        if ($formProgram->isSubmitted() && $formProgram->isValid()){

            
            $program = $formProgram->getData();

            $programManager = $doctrine->getManager();
            
            // Récupère l'intitulé du module ajouté
            $addedModule = $program->getModule()->getTitle();

            $programManager->persist($program);

            $programManager->flush();

            $this->addFlash(
                'notice',
                "Le programme $addedModule a bien été mis à jour"
            );

            return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
        }

        return $this->render('session/detailSession.html.twig',[
            'session' => $session, // Renvoi l'objet session
            'programsList' => $programsList, // Renvoi le programme selon l'ID de la session
            'internsNotInSession' => $internsNotInSession,
            'formProgram' => $formProgram->createView() // Renvoi un tableau de stagiaires non inscrits,
        ]);
    }


    // <-------- Ajouter/ Modifier les informations d'une session -------->

    /**
     * @Route("/session/edit/{id}", name= "edit_session")
     */
    public function editSession(ManagerRegistry $doctrine, Session $session = null, Request $request, Program $program) : Response
    {
        if (!$session){
            $session = new Session();
        }

        // Form modification de la session
        $form = $this->createForm(SessionType::class, $session);
        
        $form->handleRequest($request); 
        
        // Vérifie que le formulaire a été soumit et que les champs sont valides (similiaire à filter_input)
        if ($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData(); //Permet d'hydrater l'objet session
            
            $sessionManager = $doctrine->getManager(); // Récupère le manager
            $sessionManager->persist($session); // Prepare les données
            $sessionManager->flush(); // Execute la request (insert into)

            $this->addFlash(
                'notice',
                "La session a bien été mise à jour"
            );

            return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
        }

        if (!$program){
            $program = new Program();
        }

        // View pour afficher le formulaire d'ajout
        return $this->render('session/editSession.html.twig', [
            'form' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $session->getId(),
            // 'formProgram' => $formProgram->createView()
        ]);
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
            'notice',
            "Le stagiaire $unsubscribedIntern a bien été désinscrit"
        );

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);
    }


    // <-------- Inscrire un stagiaire à une session -------->

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


    // <-------- Suppression d'un module du programme -------->

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
            'notice',
            "Le module $removedModule a bien été supprimé"
        );

        return $this->redirectToRoute('session_detail', ['id' => $session->getId()]);

    }
}