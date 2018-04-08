<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Form\Admin\AdminEditCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/admin/comments")
 */
class CommentController extends Controller
{

    /**
     * @Route("/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated page list
        $em       = $this->get('doctrine')->getManager();
        $comments = $em->getRepository('AppBundle:Comment')->findByQuery(
            $request
        );

        return $this->render(
            'admin/comment/index.html.twig',
            [
                'comments' => $comments,
            ]
        );
    }

    /**
     * @Route("/new")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $comment = new Comment();
        $form    = $this->createForm(AdminEditCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($comment);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Le commentaire a bien été ajouté.'
            );

            // User is redirected to referrer page
            return $this->redirectToRoute('app_admin_comment_index');
        }

        return $this->render(
            'admin/comment/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish":
     *                                   "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Comment                 $comment
     * @param                                           $publish
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction(Request $request, Comment $comment, $publish)
    {
        $comment->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($comment);
        $em->flush();

        if ($publish) {
            $message = 'Le commentaire <strong>&quot;'.$comment
                       .'&quot;</strong> a bien été validé.';
        } else {
            $message = 'La publication du commentaire  <strong>&quot;'.$comment
                       .'&quot;</strong> a bien été refusée.';
        }

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            $message
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Comment                 $comment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Comment $comment)
    {
        $form = $this->createForm(AdminEditCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($comment);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le commentaire <strong>&quot;'.$comment
                .'&quot;</strong> a bien été modifié.'
            );

            return $this->redirectToRoute('app_admin_comment_index');
        }

        return $this->render(
            'admin/comment/edit.html.twig',
            [
                'form'    => $form->createView(),
                'comment' => $comment,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Comment                 $comment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Comment $comment)
    {
        // Deletes specified post
        $em = $this->get('doctrine')->getManager();
        $em->remove($comment);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le commentaire <strong>&quot;'.$comment
            .'&quot;</strong> a bien été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
