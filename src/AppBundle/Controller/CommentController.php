<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/comment")
 * @Security("has_role('ROLE_USER')")
 */
class CommentController extends Controller
{
    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Comment $comment)
    {
        // Only author or admin can edit comment
        if ($this->getUser() !== $comment->getUser() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');

            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($comment);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                "Votre commentaire à bien été modifié."
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'comment/edit.html.twig',
            [
                "form" => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Comment $comment)
    {
        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');

            return $this->redirectToRoute('homepage');
        }

        // Delete specified comment
        $em = $this->get('doctrine')->getManager();
        $em->remove($comment);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "Le commentaire à été supprimé.");

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
