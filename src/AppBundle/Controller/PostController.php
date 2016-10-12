<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * Displays post and associated comments.
     * Allows to publish comments.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="comment_new")
     */
    public function indexAction(Request $request, Post $post)
    {
        $listComment = $this->get('em')->repository('AppBundle:Comment')->findByPage($post->getId(), $request->query->getInt('page', 1), 5);
        $formComment = null;

        // User cannot comment when not logged in
        if ( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ) {

            $user = $this->getUser();
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setPost($post);
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            $formComment = $form->createView();

            if ( $form->isSubmitted() && $form->isValid() ) {

                $this->get('em')->save($comment);
                $this->get('session')->getFlashBag()->add('success', 'Votre commentaire a bien été enregistré.');

                // User is redirected to referrer page
                return $this->redirect( $request->headers->get('referer') );

            }
        }

        return $this->render('post/index.html.twig', [
            'form_post'    => $formComment,
            'list_comment' => $listComment,
            'post'         => $post
        ]);
    }

    /**
     * Creates new post.
     *
     * @Route("/new", name="post_new")
     */
    public function newAction(Request $request)
    {
        $formPost = null;

        // User cannot post when not logged in
        if ( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ) {

            $user = $this->getUser();
            $post = new Post();
            $post->setUser($user);
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            $formPost = $form->createView();

            if ( $form->isSubmitted() && $form->isValid() ) {

                $this->get('em')->save($post);
                $this->get('session')->getFlashBag()->add('success', 'Votre message a bien été enregistré.');

                // User is redirected to referrer page
                return $this->redirect( $request->get('back') );

            }
        }

        return $this->render('post/edit.html.twig', [
            'form_post' => $formPost
        ]);
    }

    /**
     * Edits post.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="post_edit")
     */
    public function editAction(Request $request, Post $post)
    {
        // User cannot edit when not logged in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ) {

            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour pouvoir éditer des messages.");
            return $this->redirectToRoute('app_login');

        }

        // Only author or admin can edit post
        if ( $this->getUser() !== $post->getUser() && !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à éditer ce message.");
            return $this->redirectToRoute('app_homepage');

        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            $this->get('em')->save($post);
            $this->get('session')->getFlashBag()->add('success', "Votre message <strong>&quot;{$post->getTitle()}&quot</strong> à bien été modifié.");

            // User is redirected to referrer page
            return $this->redirect( $request->get('back') );

        }

        return $this->render('post/edit.html.twig', [
            "form_post" => $form->createView()
        ]);

    }

    /**
     * Deletes post.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="post_delete")
     */
    public function deleteAction(Request $request, Post $post)
    {
        // Only author or admin can delete post
        if ( $this->getUser() !== $post->getUser() && !in_array( 'ROLE_ADMIN', $this->getUser()->getRoles() ) ) {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à supprimer ce message.");
            return $this->redirectToRoute('app_homepage');

        }

        // Deletes specified post
        $this->get('em')->remove($post);
        $this->get('em')->flush();
        $this->get('session')->getFlashBag()->add('success', "Le message <strong>&quot;{$post->getTitle()}&quot;</strong> à été supprimé.");

        // User is redirected to referrer page
        return $this->redirect( $request->get('back') );
    }

}
