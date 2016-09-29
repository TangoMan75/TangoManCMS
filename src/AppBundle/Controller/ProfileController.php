<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfileController
 * @package AppBundle\Controller
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/posts", name="profile_posts")
     */
    public function postsAction(Request $request)
    {
        return $this->render('profile/posts.html.twig', [

        ]);
    }

    /**
     * @Route("/{username}", requirements={"username" = "\w+"}, name="profile_show")
     */
    public function showAction(Request $request, $username)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneByUsername($username);

        if (!$user) {
            return $this->createNotFoundException("Cet utilisateur n'existe pas.");
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", name="profile_edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('profile/edit.html.twig', [

        ]);
    }

    /**
     * @Route("/create", name="profile_create")
     */
    public function createAction(Request $request)
    {
        return $this->render('profile/create.html.twig', [

        ]);
    }

    /**
     * @Route("/delete", name="profile_delete")
     */
    public function deleteAction(Request $request)
    {
        return $this->render('profile/delete.html.twig', [

        ]);
    }

}
