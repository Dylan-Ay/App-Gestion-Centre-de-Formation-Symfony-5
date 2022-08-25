<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/profile/message", name="app_message")
     */
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    /**
     * @Route("/send", name="app_send")
     */
    public function sendMessage(ManagerRegistry $doctrine, Request $request) : Response
    {
        $message = new Message;
        
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $message->setSender($this->getUser());

            $messageManager = $doctrine->getManager();
            $messageManager->persist($message);
            $messageManager->flush();

            $this->addFlash(
                'message',
                "Votre message a bien été envoyé"
            );

            return $this->redirectToRoute('app_message');
        }

        return $this->render('message/sendMessage.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/inbox", name="app_inbox")
     */
    public function inbox(): Response
    {
        return $this->render('message/inbox.html.twig');
    }

    /**
     * @Route("/read/{id}", name="app_read")
     */
    public function readMessage(ManagerRegistry $doctrine, Message $message): Response
    {
        $message->setIsRead(true);
        $messageManager = $doctrine->getManager();

        $messageManager->persist($message);
        $messageManager->flush();

        return $this->render('message/read.html.twig', compact("message"));
    }

    /**
     * @Route("/delete/{id}", name="app_delete")
     */
    public function deleteMessage(ManagerRegistry $doctrine, Message $message): Response
    {
        $messageManager = $doctrine->getManager();

        $messageManager->remove($message);
        $messageManager->flush();

        return $this->redirectToRoute("app_inbox");
    }
    
    /**
     * @Route("/sent", name="app_sent")
     */
    public function sentMessages()
    {
        return $this->render('message/sent.html.twig');
    }

}   
