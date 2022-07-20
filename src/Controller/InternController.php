<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Intern;
use App\Form\InternType;

class InternController extends AbstractController
{
    // Affiche la liste des stagiaires

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

    
    /**
     * @Route("/stagiaire/add", name ="add_intern")
     * @Route("/stagiaire/{id}/edit", name ="edit_intern")
     */
    public function add(ManagerRegistry $doctrine, Intern $intern = null, Request $request): Response
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
            $intern = $form->getData(); //Permet d'hydrater l'objet employe
            
            $internManager = $doctrine->getManager(); 
            $internManager->persist($intern); // Prepare les données
            $internManager->flush(); // Execute la request (insert into)

            return $this->redirectToRoute('show_intern', ['id' => $intern->getId() ]);
        }
        // View pour afficher le formulaire d'ajout
        return $this->render('intern/add.html.twig', [
            'form' => $form->createView(), // Génère le formulaire visuellement
            'edit' => $intern->getId()
        ]);
    }
    
    // Affiche le detail d'un stagiaire
    
    /**
     * @Route ("stagiaire/{id}", name= "show_intern")
     */
    public function show (Intern $intern) : Response
    {
        return $this->render('intern/show.html.twig', [
            "intern" => $intern
        ]);
    }
}
