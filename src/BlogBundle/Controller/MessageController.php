<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Message;
use BlogBundle\Entity\User;
use BlogBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{
    /**
     * @Route("/user/{id}/message/{articleId}", name="user_message")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $articleId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMessageAction(Request $request,$articleId, $id)
    {


        $currentUser = $this->getUser();
        $recipient = $this->getDoctrine()->getRepository(User::class)->find($id);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $message
                ->setSender($currentUser)
                ->setRecipient($recipient)
                ->setIsReader(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message sent successfully!");
            return $this->redirectToRoute("article_view", ['id' => $articleId]);
        }


        return $this->render('user/send_message.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user/mailbox", name="user_mailbox")
     */

    public function mailBoxAction()
    {

        $currentUserId = $this->getUser()->getId();

        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($currentUserId);

        $messages = $this
            ->getDoctrine()
            ->getRepository(Message::class)
            ->findBy(['recipient' => $user],['dateAdded' => 'DESC']);


        return $this->render("user/mailbox.html.twig", ['messages' => $messages]);
    }

    /**
     * @Route("/user/mailbox/message/{id}", name="user_current_message")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function messageAction(Request $request, $id)
    {

        $message = $this
            ->getDoctrine()
            ->getRepository(Message::class)
            ->find($id);

        $message->setIsReader(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();


        $sendMessage = new Message();
        $form = $this->createForm(MessageType::class, $sendMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $sendMessage
                ->setSender($this->getUser())
                ->setRecipient($message->getSender())
                ->setIsReader(false);

            $em->persist($sendMessage);
            $em->flush();

            $this->addFlash("message", "Message sent successfully!");
            return $this->redirectToRoute("user_current_message", ['id' => $id]);
        }

        return $this->render("user/message.html.twig", ['message' => $message, 'form' => $form->createView()]);
    }
}
