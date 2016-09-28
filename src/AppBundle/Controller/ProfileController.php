<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/posts")
     */
    public function postsAction(Request $request)
    {
        return $this->render('Profile/posts.html.twig', [

        ]);
    }

    /**
     * @Route("/show/{username}", requirements={"username" = "\w+"})
     */
    public function showAction(Request $request, $username)
    {
        return $this->render('Profile/show.html.twig', [
            'username' => $username,
        ]);
    }

    /**
     * @Route("/edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('Profile/edit.html.twig', [

        ]);
    }

    /**
     * @Route("/create")
     */
    public function createAction(Request $request)
    {
        return $this->render('Profile/create.html.twig', [

        ]);
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction(Request $request)
    {
        return $this->render('Profile/delete.html.twig', [

        ]);
    }

}
