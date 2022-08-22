<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Module;
use App\Form\CategoryType;
use App\Form\ModuleType;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class FormationController extends AbstractController
{
    // <-------- Affiche la liste des formations avec leurs sessions dans la page "formations"  -------->

    /**
     * @Route("/formations", name="app_formation")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
            
        $formations = $doctrine->getRepository(Formation::class)->findAll();

        return $this->render('formation/formationsList.html.twig', [
            'formations' => $formations,
        ]);
    }
    

    // <-------- Affiche la liste des modules dans la page "gestion des modules" -------->

    /**
     * @Route("/modules", name="app_modules")
     */
    public function modulesList(ManagerRegistry $doctrine): Response
    {
        $modules = $doctrine->getRepository(Module::class)->findAll();

        return $this->render('formation/modulesList.html.twig', [
            'modulesList' => $modules
        ]);
    }


    // <-------- Ajoute ou modifie un module dans la page "gestion des modules" -------->

    /**
     * @Route("/module/add", name="add_module")
     * @Route("/module/{id}/edit", name= "edit_module")
     */
    public function editModule(ManagerRegistry $doctrine, Module $module = null, Request $request) : Response
    {
        if (!$module){
            $module = new Module();
        }

        $form = $this->createForm(ModuleType::class, $module);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

                $module = $form->getData();

                $moduleManager = $doctrine->getManager();

                $moduleTitle = $module->getTitle();

                $moduleManager->persist($module);
                $moduleManager->flush();
                
                $this->addFlash(
                    'notice',
                    "Le module $moduleTitle a bien été ajouté"
                );
            
                return $this->redirectToRoute('app_modules');
        }

        return $this->render('formation/editModule.html.twig', [
            'form' => $form->createView(),
            'edit' => $module->getId(),
        ]);
    }


    // <-------- Supprime un module dans la page "gestion des modules" -------->

    /**
     * @Route("/module/delete/{id}", name="module_delete")
     */
    public function deleteModule(ManagerRegistry $doctrine, Module $module) : Response
    {
        $moduleManager = $doctrine->getManager();

        $moduleTitle = $module->getTitle();

        $moduleManager->remove($module);
        $moduleManager->flush();

        $this->addFlash(
            'notice',
            "Le module $moduleTitle a bien été supprimé"
        );

        return $this->redirectToRoute('app_modules');
    }

    // <-------- Affiche la liste des catégories dans la page "gestion des catégories" -------->

    /**
     * @Route("/catégories", name= "app_categories")
     */
    public function categoriesList(ManagerRegistry $doctrine) : Response
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('formation/categoriesList.html.twig', [
            'categoriesList' => $categories
        ]);
    }

    // <-------- Ajoute ou modifie une catégorie dans la page "gestion des catégories" -------->

    /**
     * @Route("/categorie/add", name= "add_category")
     * @Route("/categorie/{id}/edit", name= "edit_category")
     */

    public function editCategory(ManagerRegistry $doctrine, Category $category = null, Request $request) : Response
    {
        if (!$category){
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $category = $form->getData();

            $categoryManager = $doctrine->getManager();

            $categoryTitle = $category->getTitle();

            $categoryManager->persist($category);
            $categoryManager->flush();

            $this->addFlash(
                'notice',
                "La catégorie $categoryTitle a bien été ajoutée"
            );

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('formation/editCategory.html.twig', [
            'form' => $form->createView(),
            'edit' => $category->getId()
        ]);
    }


    // <-------- Supprime une catégorie dans la page "gestion des catégories" -------->

    /**
     * @Route("/categorie/{id}/delete", name="delete_category")
     */
    public function deleteCategory(ManagerRegistry $doctrine, Category $category) : Response
    {
        $categoryManager = $doctrine->getManager();

        $categoryTitle = $category->getTitle();

        $categoryManager->remove($category);
        $categoryManager->flush();

        $this->addFlash(
            'notice',
            "La catégorie $categoryTitle a bien été supprimée"
        );

        return $this->redirectToRoute('app_categories');
    }

    
    // <-------- Permet de générer un PDF avec le programme de la formation -------->

    /**
     * @Route("/formation/programme/{id}", name="programme-formation")
     */
     public function formationProgram(Formation $formation)
     {
        // On définit les options du PDF
        $pdfOptions = new Options();

        // Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // On instancie Dompdf
        $domPdf = new Dompdf($pdfOptions);

        // On génère le html
        $html = $this->renderView('formation/mypdf.html.twig',[
            'formation' => $formation
            ]);

        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        // On génère un nom de fichier

        $fichier = 'Programme de la formation';

        // On renvoie le PDF au navigateur
        $domPdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }

}