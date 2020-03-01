<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy([], ['viewCount' => 'desc', 'dateAdded' => 'desc']);

//        $paginator = $this->get('knp_paginator');
//
//        $pagination = $paginator->paginate(
//            $articles, /* query NOT result */
//            $request->query->getInt('page', 1), /*page number*/
//            4 /*limit per page*/
//        );

        return $this->render('default/index.html.twig',
            ['articles' => $articles]);
    }
}
