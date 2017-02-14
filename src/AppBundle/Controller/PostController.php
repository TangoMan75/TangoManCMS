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
     * Display post with comments.
     * Allow to publish comments.
     *
     * @Route("/show/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function showAction(Request $request, $slug)
    {
        $post = $this->get('em')->repository('AppBundle:Post')->findOneBy(['slug' => $slug]);

        $listComment = $this->get('em')->repository('AppBundle:Comment')->findAllPaged(
            $post, $request->query->getInt('page', 1), 5
       );
        $formComment = null;

        // User cannot comment when not logged in
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $user = $this->getUser();
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setPost($post);

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            $formComment = $form->createView();

            if ($form->isSubmitted() && $form->isValid()) {
                $this->get('em')->save($comment);
                $this->get('session')->getFlashBag()->add('success', 'Votre commentaire a bien été enregistré.');

                // User is redirected to referrer page
                return $this->redirect($request->getUri());
            }
        }
        return $this->render('post/index.html.twig', [
            'formPost'     => $formComment,
            'list_comment' => $listComment,
            'post'         => $post
        ]);
    }

    /**
     * Creates new post.
     *
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        // User cannot post when not logged in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $post = new Post();
        $post->setUser($user);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $formPost = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('em')->save($post);
            $this->get('session')->getFlashBag()->add('success', 'Le message intitulé <strong>'. $post->getTitle() .'</strong> a bien été enregistré.');

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }
        return $this->render('post/edit.html.twig', [
            'formPost' => $formPost
        ]);
    }

    /**
     * Edits post.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Post $post)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('em')->save($post);
            $this->get('session')->getFlashBag()->add('success',
                'Votre message <strong>&quot;'. $post->getTitle() .'&quot</strong> à bien été modifié.'
           );
            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }
        return $this->render('post/edit.html.twig', [
            'formPost' => $form->createView()
        ]);
    }

    /**
     * Deletes post.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Post $post)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can delete post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('homepage');
        }

        // Deletes specified post
        $this->get('em')->remove($post);
        $this->get('em')->flush();
        $this->get('session')->getFlashBag()->add('success',
            'Le message <strong>&quot;'. $post->getTitle() .'&quot;</strong> à été supprimé.'
       );
        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
