<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Module;
use App\Entity\Session;
use App\Form\ModuleType;
use Symfony\Component\HttpFoundation\Request;

class FormationController extends AbstractController
{
    // Affiche la liste des formations avec leurs sessions
    /**
     * @Route("/formations", name="app_formation")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $formations = $doctrine->getRepository(Formation::class)->findAll();

        $session = $doctrine->getRepository(Session::class)->findAll();

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
            'session' => $session
        ]);
    }
    
    //Affiche la liste des modules

    /**
     * @Route("/modules", name="app_modules")
     */
    public function modulesList(ManagerRegistry $doctrine): Response
    {
        $modules = $doctrine->getRepository(Module::class)->findAll();

        return $this->render('formation/modules.html.twig', [
            'modulesList' => $modules
            // 'modulesCategories' => $module
        ]);
    }

    // Ajoute et modifie un module

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

        return $this->render('formation/addModule.html.twig', [
            'formAddModule' => $form->createView(),
            'edit' => $module->getId(),
        ]);
    }

    // Ajoute une catégorie

    public function addCategory(ManagerRegistry $doctrine, Category $category = null, Request $request) : Response
    {
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
                "Le module $categoryTitle a bien été ajouté"
            );

        }

        return $this->render('formation/addCategory.html.twig', [
            'formAddCategory' => $form->createView()
        ]);
    }

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

    // public function detailFormation()
    // {
    //     // TODO: Implement this method
    // }

    // public function detailProgramm()
    // {
    //     // TODO: implement this method
    // }


}