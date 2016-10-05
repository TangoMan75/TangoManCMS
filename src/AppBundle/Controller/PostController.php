<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="post_edit")
     */
    public function editAction(Request $request, Post $post)
    {
        if (!$post) {

            $this->get('session')->getFlashBag()->add('error', "Ce message n'existe pas.");

        } else {

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->get('em')->save($post);

                $this->get('session')->getFlashBag()->add('success', "Votre message <strong>&quot;{$post->getTitle()}&quot</strong> à bien été modifié.");

                return $this->redirectToRoute('app_homepage');

            } else {

                return $this->render('post/edit.html.twig', [
                    "form_post" => $form->createView()
                ]);
            }
        }
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="post_delete")
     */
    public function deleteAction(Request $request, Post $post)
    {
        if (!$post) {

            $this->get('session')->getFlashBag()->add('error', "Ce message n'existe pas.");

        } else {

            // Deletes specified post
            $this->get('em')->remove($post);
            $this->get('em')->flush();

            $this->get('session')->getFlashBag()->add('success', "Le message <strong>&quot;{$post->getTitle()}&quot;</strong> à été supprimé.");
        }

        return $this->redirectToRoute('app_homepage');

    }

}
