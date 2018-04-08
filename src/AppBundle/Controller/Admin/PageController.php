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

use AppBundle\Entity\Page;
use AppBundle\Form\Admin\AdminNewPageType;
use AppBundle\Form\Admin\AdminEditPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/admin/pages")
 */
class PageController extends Controller
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
        $em    = $this->get('doctrine')->getManager();
        $pages = $em->getRepository('AppBundle:Page')->findByQuery($request);

        return $this->render(
            'admin/page/index.html.twig',
            [
                'pages' => $pages,
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
        $page = new Page();
        $form = $this->createForm(AdminNewPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'La page <strong>&quot;'.$page
                .'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/page/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish":
     *                                   "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Page                    $page
     * @param                                           $publish
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction(Request $request, Page $page, $publish)
    {
        $page->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($page);
        $em->flush();

        if ($publish) {
            $message = 'La page <strong>&quot;'.$page
                       .'&quot;</strong> a bien été publiée.';
        } else {
            $message = 'La publication de la page <strong>&quot;'.$page
                       .'&quot;</strong> a bien été annulée.';
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
     * @param \AppBundle\Entity\Page                    $page
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createForm(AdminEditPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'La page <strong>&quot;'.$page
                .'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/page/edit.html.twig',
            [
                'form' => $form->createView(),
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Page                    $page
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Page $page)
    {
        if ( ! $this->get('security.authorization_checker')->isGranted(
            'ROLE_SUPER_ADMIN'
        )) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        // Deletes specified section
        $em = $this->get('doctrine')->getManager();
        $em->remove($page);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La page <strong>&quot;'.$page
            .'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
