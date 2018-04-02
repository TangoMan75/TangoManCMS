<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\AdminEditTagType;
use AppBundle\Form\Admin\AdminNewTagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagController
 * @Route("/admin/tags")
 *core/pdo.php
 */
class TagController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated tag list
        $em   = $this->get('doctrine')->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findByQuery($request);

        return $this->render(
            'admin/tag/index.html.twig',
            [
                'tags' => $tags,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $tag  = new Tag();
        $form = $this->createForm(AdminNewTagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new tag
            $em = $this->get('doctrine')->getManager();
            $em->persist($tag);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'étiquette <strong>&quot;'.$tag
                .'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/tag/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Tag $tag)
    {
        $form = $this->createForm(AdminEditTagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited tag
            $em = $this->get('doctrine')->getManager();
            $em->persist($tag);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'L\'étiquette <strong>&quot;'.$tag
                .'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/tag/edit.html.twig',
            [
                'form' => $form->createView(),
                'tag'  => $tag,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        // Deletes specified tag
        $em = $this->get('doctrine')->getManager();
        $em->remove($tag);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'étiquette <strong>&quot;'.$tag
            .'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
