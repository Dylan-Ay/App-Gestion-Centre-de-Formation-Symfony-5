<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Category;
use App\Entity\Module;
use App\Repository\ModuleRepository;

class FormationController extends AbstractController
{
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

    /**
     * @Route("/modules", name="app_module")
     */
    public function modulesList(ManagerRegistry $doctrine, ModuleRepository $module): Response
    {

        $modules = $doctrine->getRepository(Module::class)->findAll();

        return $this->render('formation/modules.html.twig', [
            'modulesList' => $modules
            // 'modulesCategories' => $module
        ]);
    }

    public function detailFormation()
    {
        // TODO: Implement this method
    }

    public function detailProgramm()
    {
        // TODO: implement this method
    }


}