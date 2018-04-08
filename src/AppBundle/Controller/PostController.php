<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Vote;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Form\EditPostType;
use AppBundle\Form\NewPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PostController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/post")
 */
class PostController extends Controller
{

    /**
     * @Route()
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();

        if ($this->get('security.authorization_checker')->isGranted(
            'ROLE_ADMIN'
        )) {
            $posts = $em->getRepository('AppBundle:Post')->findByQuery(
                $request
            );
        } else {
            $posts = $em->getRepository('AppBundle:Post')->findByQuery(
                $request,
                ['published' => true]
            );
        }

        $formView = null;

        if ($this->get('security.authorization_checker')->isGranted(
            'IS_AUTHENTICATED_REMEMBERED'
        )) {
            $post = new Post();
            $post->setUser($this->getUser());
            $post->setType('post');

            $form = $this->createForm(NewPostType::class, $post);
            $form->handleRequest($request);
            $formView = $form->createView();

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
                'form'  => $formView,
                'posts' => $posts,
            ]
        );
    }

    /**
     * Displays post by tag.
     * @Route("/index/{tag}", requirements={"tag": "[\w]+"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $tag
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexByTagAction(Request $request, $tag)
    {
        $em  = $this->get('doctrine')->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->findOneByName(
            ['name' => $tag]
        );

        if ($this->get('security.authorization_checker')->isGranted(
            'ROLE_ADMIN'
        )) {
            $posts = $em->getRepository('AppBundle:Post')->findByQuery(
                $request,
                [
                    'tag' => $tag,
                ]
            );
        } else {
            $posts = $em->getRepository('AppBundle:Post')->findByQuery(
                $request,
                [
                    'published' => true,
                    'tag'       => $tag,
                ]
            );
        }

        $formView = null;

        if ($this->get('security.authorization_checker')->isGranted(
            'IS_AUTHENTICATED_REMEMBERED'
        )) {
            $post = new Post();
            $post->setUser($this->getUser());
            $post->setType('post');

            $form = $this->createForm(NewPostType::class, $post);
            $form->handleRequest($request);
            $formView = $form->createView();

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
                'form'  => $formView,
                'posts' => $posts,
            ]
        );
    }

    /**
     * Display post with comments.
     * Allow to publish comments.
     *
     * @Route("/show/{slug}", requirements={"slug": "[\w-]+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $slug
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $slug)
    {
        $em   = $this->get('doctrine')->getManager();
        $post = $em->getRepository('AppBundle:Post')->findOneBy(
            ['slug' => $slug]
        );

        if ( ! $post) {
            throw $this->createNotFoundException('Cet article n\'existe pas.');
        }

        $post->addView();
        $em->persist($post);
        $em->flush();

        if ($this->get('security.authorization_checker')->isGranted(
            'ROLE_ADMIN'
        )) {
            $comments = $em->getRepository('AppBundle:Comment')->findByQuery(
                $request,
                ['post' => $post]
            );
        } else {
            $comments = $em->getRepository('AppBundle:Comment')->findByQuery(
                $request,
                ['post' => $post, 'published' => true]
            );
        }

        $form = null;

        // User cannot comment when not logged in
        if ($this->get('security.authorization_checker')->isGranted(
            'IS_AUTHENTICATED_REMEMBERED'
        )) {

            $comment = new Comment();
            $comment->setUser($this->getUser());
            $comment->setPost($post);

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($comment);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre commentaire a bien été enregistré.'
                );

                // User is redirected to same page
                return $this->redirect($request->getUri());
            }
        }

        return $this->render(
            'post/show.html.twig',
            [
                'form'     => $form->createView(),
                'comments' => $comments,
                'post'     => $post,
            ]
        );
    }

    /**
     * @Route("/new")
     * @Security("has_role('ROLE_USER')")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setUser($this->getUser());
        $post->setType('post');

        $form = $this->createForm(NewPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'article intitulé <strong>'.$post
                .'</strong> a bien été enregistré.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER')")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Post $post)
    {
        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser()
            && ! $this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Vous n\'êtes pas autorisé à réaliser cette action.'
            );

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
                'Votre article <strong>&quot;'.$post
                .'&quot</strong> à bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER')")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Post $post)
    {
        // Only author or admin can edit post
        if ($this->getUser() !== $post->getUser()
            && ! $this->get('security.authorization_checker')->isGranted(
                'ROLE_ADMIN'
            )
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Vous n\'êtes pas autorisé à réaliser cette action.'
            );

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
     * @Route("/vote/{slug}/{value}", requirements={"slug": "[\w-]+", "value":
     *                                "(up|down)"})
     * @Security("has_role('ROLE_USER')")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $slug
     * @param                                           $value
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function voteAction(Request $request, $slug, $value)
    {
        $em   = $this->get('doctrine')->getManager();
        $post = $em->getRepository('AppBundle:Post')->findOneBy(
            ['slug' => $slug]
        );
        $user = $this->getUser();
        $vote = $em->getRepository('AppBundle:Vote')->findOneBy(
            ['user' => $user, 'post' => $post]
        );

        // When not found creates new vote
        if ( ! $vote) {
            $vote = new Vote();
            // Links vote, user & posts
            $vote->setUser($user);
            $vote->setPost($post);
        }

        switch ($value) {
            case 'up':
                $vote->setValue(1);
                break;

            case 'down':
                $vote->setValue(0);
                break;
        }

        $em->persist($vote);
        $em->flush();

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
