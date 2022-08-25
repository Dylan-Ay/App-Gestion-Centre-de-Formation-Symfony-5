<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Intern;
use App\Entity\Session;
use App\Form\InternType;

class InternController extends AbstractController
{
    // <-------- Affiche la liste des stagiaires -------->

    /**
     * @Route ("/profile/interns", name= "app_interns")
     */
    public function index (ManagerRegistry $doctrine) : Response
    {
        $interns = $doctrine->getRepository(Intern::class)->findAll();

        return $this->render('intern/index.html.twig', [
            'internsList' => $interns
        ]);
    }


    // <-------- Ajout ou modifie les informations d'un stagiaire -------->
    
    /**
     * @Route("/stagiaire/add", name ="add_intern")
     * @Route("/stagiaire/{id}/edit", name ="edit_intern")
     */
    public function editIntern(ManagerRegistry $doctrine, Intern $intern = null, Request $request): Response
    {
        if (!$intern){
            $intern = new Intern();
        }

        // Crée un form en se basant sur l'objet Intern, il va récupérer les propriétés de la class intern
        $form = $this->createForm(InternType::class, $intern);
        
        // Permet d'analyser les données insérées dans le form et de récupérer les données pour les mettre dans le formulaire
        $form->handleRequest($request); 
        
        // Vérifie que le formulaire a été soumit et que les champs sont valides (similiaire à filter_input)
        if ($form->isSubmitted() && $form->isValid()) {

            // if ($session->leftPlacesNumber() === 0){
            //     return $this->redirectToRoute('add_intern');
            // }

            $intern = $form->getData(); //Permet d'hydrater l'objet employe
            
            $internManager = $doctrine->getManager(); // Récupère le manager
            $internManager->persist($intern); // Prepare les données
            $internManager->flush(); // Execute la request (insert into)

            $this->addFlash(
                'notice',
                "La fiche stagiaire a bien été mise à jour"
            );

            return $this->redirectToRoute('intern_detail', ['id' => $intern->getId()]);
        }
        // View pour afficher le formulaire d'ajout
        return $this->render('intern/editIntern.html.twig', [
            'form' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $intern->getId()
        ]);
    }


    // <-------- Supprime un stagiaire -------->

    /**
     * @Route("stagiaire/{id}/delete", name="delete_intern")
     */
    public function deleteIntern (ManagerRegistry $doctrine, Intern $intern): Response
    {
        $entityManager = $doctrine->getManager();

        $internDeleted = $intern->getFullName();

        $entityManager->remove($intern);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            "Le stagiaire $internDeleted a bien été supprimé"
        );

        return $this->redirectToRoute('app_interns');
    }
    
    // <-------- Affiche le detail d'un stagiaire -------->
    
    /**
     * @Route ("stagiaire/{id}", name= "intern_detail")
     */
    public function internDetail (Intern $intern, ManagerRegistry $doctrine) : Response
    {
        $session = $doctrine->getRepository(Session::class)->findAll();

        return $this->render('intern/detail.html.twig', [
            "intern" => $intern,
            "session" => $session
        ]);
    }
}