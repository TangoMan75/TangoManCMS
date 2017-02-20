<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Edit comment.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Comment $comment)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');
            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->get('em')->save($comment);
            $this->get('session')->getFlashBag()->add('success',
                "Votre commentaire à bien été modifié.");
            // User is redirected to referrer page
            return $this->redirect( $request->get('callback') );
        }

        return $this->render('post/edit.html.twig', [
            "formPost" => $form->createView()
        ]);

    }

    /**
     * Delete comment.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Comment $comment)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');
            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');
            return $this->redirectToRoute('homepage');
        }

        // Delete specified comment
        $this->get('em')->remove($comment);
        $this->get('em')->flush();
        $this->get('session')->getFlashBag()->add('success', "Le commentaire à été supprimé.");

        // User is redirected to referrer page
        return $this->redirect( $request->get('callback') );
    }

}
