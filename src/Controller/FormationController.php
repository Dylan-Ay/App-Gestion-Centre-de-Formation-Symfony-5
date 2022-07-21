<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class FormationController extends AbstractController
{
    // Affiche la liste des formations

    /**
     * @Route("/formations", name="app_formation")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $formations = $doctrine->getRepository(Formation::class)->findAll();

        return $this->render('formation/index.html.twig', [
            'formationsList' => $formations,
        ]);
    }

    //Affiche la liste des modules

    /**
     * @Route("/modules", name="app_modules")
     */
    public function modulesList(ManagerRegistry $doctrine, ModuleRepository $module): Response
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
    public function addModule(ManagerRegistry $doctrine, Module $module = null, Request $request) : Response
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

    /**
     * @Route("/module/delete/{id}", name="module_delete")
     */
    public function delete(ManagerRegistry $doctrine, Module $module) : Response
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