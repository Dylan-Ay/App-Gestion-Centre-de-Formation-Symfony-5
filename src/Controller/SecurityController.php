<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PswdType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Svg\Tag\Rect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    // <-------- Modifie le profil d'un user  -------->

    /**
     * @Route("security/editProfil/{id}", name="edit_profil")
     */
    public function editProfil(ManagerRegistry $doctrine, Request $request, User $user = null): Response 
    {
        $form = $this->createForm(UserType::class, $user);
        
        // Permet d'analyser les données insérées dans le form et de récupérer les données pour les mettre dans le formulaire
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $form->getData(); //Permet d'hydrater l'objet employe
            
            $entityManager = $doctrine->getManager(); // Récupère le ManagerRegistry
            $entityManager->persist($user); // Prépare les données
            $entityManager->flush();    // Execute la request
            
            $this->addFlash(
                'notice',
                "Vos informations ont bien été mises à jour"
            );
        }
        
        return $this->render('security/editProfil.html.twig', [
            'form' => $form->createView()
        ]);
    }
    

    // <-------- Modifie le password d'un user -------->

    /**
     * @Route("security/editPassword/{id}", name="edit_password")
     */
    public function editPassword(EntityManagerInterface $entityManager, Request $request, User $user = null, UserPasswordHasherInterface $userPasswordHasher) : Response
    {
        
        $form = $this->createForm(PswdType::class);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            
            $entityManager->persist($user); // Prépare les données
            $entityManager->flush();    // Execute la request
            
            $this->addFlash(
                'notice',
                "Votre mot de passe a bien été mis à jour"
            );
        }
        
        return $this->render('security/editPassword.html.twig', [
            'form' => $form->createView()
        ]);

    }


    // <-------- Affiche le profil d'un user -------->

    /**
     * @Route("/profile/security/personal-profile", name="app_profil")
     */
    public function showProfil(ManagerRegistry $doctrine): Response
    {
        $user = $doctrine->getRepository(User::class)->findAll();
    
        return $this->render('security/profil.html.twig', [
            'user' => $user
        ]);
    }

}
