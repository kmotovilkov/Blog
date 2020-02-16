<?php

namespace BlogBundle\Controller;


use BlogBundle\Entity\Article;
use BlogBundle\Entity\Comment;
use BlogBundle\Entity\User;
use BlogBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{

    /**
     * @Route("/article/{id}/comment", name="add_comment")
     * @param Request $request
     * @param Article $article
     * @return RedirectResponse
     */
    public function addComment(Request $request, Article $article)
    {
        $user = $this->getUser();

        /** @var User $author */

        $author = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($user->getId());

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $comment->setAuthor($author);
        $comment->setArticle($article);

        $author->addComments($comment);
        $article->addComment($comment);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('article_view',
            ['id' => $article->getId()]);
    }
}
