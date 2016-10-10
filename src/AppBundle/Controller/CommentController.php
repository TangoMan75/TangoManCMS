<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
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
     * Creates new comment.
     *
     * @Route("/comment/{id}", name="comment_new")
     */
    public function newAction(Request $request, Post $id)
    {
        $formComment = null;

        // User cannot comment when not logged in
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            $user = $this->getUser();
            $comment = new Comment();
            $comment->setUser($user);
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            $formComment = $form->createView();

            // referrer url is cached into session when form is not yet submitted
            if ( !$form->isSubmitted() ) {

                $this->get('session')->set('callback_url', $request->headers->get('referer'));

            }

            if ( $form->isSubmitted() && $form->isValid() ) {

                $this->get('em')->save($comment);
                $this->get('session')->getFlashBag()->add('success', 'Votre commentaire a bien été enregistré.');

                // User is redirected to referrer page
                return $this->redirect( $this->get('session')->get('callback_url') );

            }
        }

        return $this->render('comment/edit.html.twig', [
            'form_comment' => $formComment
        ]);
    }

    /**
     * Edits comment.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="comment_edit")
     */
    public function editAction(Request $request, Post $comment)
    {
        // User cannot edit when not logged in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ) {

            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour pouvoir éditer des messages.");
            return $this->redirectToRoute('app_login');

        }

        // Only author or admin can edit comment
        if ( $this->getUser() !== $comment->getUser() && !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à éditer ce message.");
            return $this->redirectToRoute('app_homepage');

        }

        $form = $this->createForm(PostType::class, $comment);
        $form->handleRequest($request);

        // referrer url is cached into session when form is not yet submitted
        if ( !$form->isSubmitted() ) {

            $this->get('session')->set('callback_url', $request->headers->get('referer'));

        }

        if ( $form->isSubmitted() && $form->isValid() ) {

            $this->get('em')->save($comment);
            $this->get('session')->getFlashBag()->add('success', "Votre message <strong>&quot;{$comment->getTitle()}&quot</strong> à bien été modifié.");

            // User is redirected to referrer page
            return $this->redirect( $this->get('session')->get('callback_url') );

        }

        return $this->render('comment/edit.html.twig', [
            "form_comment" => $form->createView()
        ]);

    }

    /**
     * Deletes comment.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="comment_delete")
     */
    public function deleteAction(Request $request, Post $comment)
    {
        // Only author or admin can delete comment
        if ( $this->getUser() !== $comment->getUser() && !in_array( 'ROLE_ADMIN', $this->getUser()->getRoles() ) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à supprimer ce message.");
            return $this->redirectToRoute('app_homepage');

        }

        // Deletes specified comment
        $this->get('em')->remove($comment);
        $this->get('em')->flush();
        $this->get('session')->getFlashBag()->add('success', "Le message <strong>&quot;{$comment->getTitle()}&quot;</strong> à été supprimé.");

        // User is redirected to referrer page
        return $this->redirect( $request->headers->get('referer') );
    }

}
