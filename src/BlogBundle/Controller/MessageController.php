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
     * @Route("/user/{id}/message", name="user_message")
    * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
    * @param $id
    * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $id)
    {

        $currentUser = $this->getUser();
        $recipient = $this->getDoctrine()->getRepository(User::class)->find($id);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $message->setSender($currentUser)->setRecipient($recipient);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message sent successfully!");
            return $this->redirectToRoute("user_message", ['id' => $id]);
        }


        return $this->render('user/send_message.html.twig',['form'=>$form->createView()]);
    }
}
