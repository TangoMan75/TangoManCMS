<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    /**
     * @Route("/edit/{id}", name="post_edit")
     */
    public function editAction(Request $request, Post $post)
    {
        return $this->render('post/edit.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/delete/{id}", name="post_delete")
     */
    public function deleteAction(Request $request, Post $post)
    {
        if (!$post) {
            $this->get('session')->getFlashBag()->add('error', "Ce message n'existe pas.");
        } else {

            // Deletes specified post
            $this->get('em')->remove($post);
            $this->get('em')->flush();

            $this->get('session')->getFlashBag()->add('success', "Le message &quot;{$post->getTitle()}&quot; à été supprimé.");
        }
        return $this->redirectToRoute('app_homepage');
    }

}
