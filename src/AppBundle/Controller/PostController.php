<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Stats;
use AppBundle\Entity\Tag;
use AppBundle\Form\CommentType;
use AppBundle\Form\EditPostType;
use AppBundle\Form\NewPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * Displays post by tag.
     * @Route()
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $posts = $em->getRepository('AppBundle:Post')
                ->findAllPaged($request->query->getInt('page', 1), 5, false);
        } else {
            $posts = $em->getRepository('AppBundle:Post')
                ->findAllPaged($request->query->getInt('page', 1), 5);
        }

        $formPost = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $post = new Post();
            $post->setUser($this->getUser());
            $form = $this->createForm(NewPostType::class, $post);
            $form->handleRequest($request);
            $formPost = $form->createView();

            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre article &quot;'.$post.'&quot; a bien été enregistré.'
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'formPost' => $formPost,
                'posts'    => $posts,
            ]
        );
    }

    /**
     * Displays post by tag.
     * @Route("/index/{tag}", requirements={"tag": "[\w]+"})
     */
    public function indexByTagAction(Request $request, $tag)
    {
        $em = $this->get('doctrine')->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->findOneByName(['name' => $tag]);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $posts = $em->getRepository('AppBundle:Post')
                ->findByTagPaged($tag, $request->query->getInt('page', 1), 5, false);
        } else {
            $posts = $em->getRepository('AppBundle:Post')
                ->findByTagPaged($tag, $request->query->getInt('page', 1), 5);
        }

        $formPost = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $post = new Post();
            $post->setUser($this->getUser());
            $form = $this->createForm(NewPostType::class, $post);
            $form->handleRequest($request);
            $formPost = $form->createView();

            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre article &quot;'.$post.'&quot; a bien été enregistré.'
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'formPost' => $formPost,
                'posts'    => $posts,
            ]
        );
    }

    /**
     * Display post with comments.
     * Allow to publish comments.
     * @Route("/show/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $post = $em->getRepository('AppBundle:Post')->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('Cet article n\'existe pas.');
        }

        $this->addView($post);

        $listComment = $em->getRepository('AppBundle:Comment')->findAllPaged(
            $post,
            $request->query->getInt('page', 1),
            5
        );

        $formComment = null;

        // User cannot comment when not logged in
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $comment = new Comment();
            $comment->setUser($this->getUser());
            $comment->setPost($post);

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            $formComment = $form->createView();

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($comment);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Votre commentaire a bien été enregistré.');

                // User is redirected to same page
                return $this->redirect($request->getUri());
            }
        }

        return $this->render(
            'post/show.html.twig',
            [
                'formPost'     => $formComment,
                'list_comment' => $listComment,
                'post'         => $post,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');

            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $post->setUser($this->getUser());
        $form = $this->createForm(NewPostType::class, $post);
        $form->handleRequest($request);
        $formPost = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'article intitulé <strong>'.$post.'</strong> a bien été enregistré.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'formPost' => $formPost,
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Post $post)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');

            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');

            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(EditPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Votre article <strong>&quot;'.$post.'&quot</strong> à bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'formPost' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Post $post)
    {
        // User must log in
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->get('session')->getFlashBag()->add('error', 'Vous devez être connecté pour réaliser cette action.');

            return $this->redirectToRoute('app_login');
        }

        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser() && !$this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add('error', 'Vous n\'êtes pas autorisé à réaliser cette action.');

            return $this->redirectToRoute('homepage');
        }

        // Deletes specified post
        $em = $this->get('doctrine')->getManager();
        $em->remove($post);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'article <strong>&quot;'.$post.'&quot;</strong> à été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * Adds one upvote.
     * @Route("/like/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function likeAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $post = $em->getRepository('AppBundle:Post')->findOneBy(['slug' => $slug]);
        $user = $this->getUser();
        $stats = $post->getStats();

        // When not found checks if user has stats
        if (!$stats) {
            $stats = $user->getStats();
        }

        // When not found creates new stats
        if (!$stats) {
            $stats = new Stats();
            // Links stats, user & posts
            $stats->addPost($post);
            $stats->addUser($user);
            $post->setStats($stats);
            $user->setStats($stats);
        }

        $stats->addLike();

        $em->persist($stats);
        $em->persist($post);
        $em->persist($user);
        $em->flush();

//        dump($user);
//        dump($post);
//        dump($stats);
//
//        dump($user->getStats());
//        dump($post->getStats());
//        dump($stats->getPosts());
//        dump($stats->getUsers());

        die();

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @param Post $post
     */
    public function addView(Post $post)
    {
        $em = $this->get('doctrine')->getManager();

        // Get post stats
        $stats = $post->getStats();

        // When not found creates new stats object
        if (!$stats) {
            $stats = new Stats();

            // Links stats & posts
            // $stats->addPost($post);
            $post->setStats($stats);
        }

        $stats->addView();
        $em->persist($stats);
        $em->persist($post);
        $em->flush();
    }
}
