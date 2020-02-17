<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("admin")
 * Class AdminController
 * @package BlogBundle\Controller
 */
class AdminController extends Controller
{

    /**
     * @Route("/", name="all_users" )
     * @return Response
     */
    public function indexAction()
    {


        $allUsers = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('admin/admin.html.twig',
            ['allUsers' => $allUsers]);
    }

    /**
     * @Route("/user_profile/{id}", name="admin_user_profile" )
     * @param $id
     * @return Response
     */
    public function userProfile($id)
    {
        $user= $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        return $this->render('admin/user_profile.html.twig',
            ['user' => $user]);
    }
}
