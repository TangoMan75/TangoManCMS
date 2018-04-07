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

use AppBundle\Entity\Site;
use AppBundle\Form\Admin\AdminNewSiteType;
use AppBundle\Form\Admin\AdminEditSiteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SiteController
 * @Route("/admin/sites")
 *core/pdo.php
 */
class SiteController extends Controller
{

    /**
     * @Route("/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated site list
        $em = $this->get('doctrine')->getManager();
        $sites = $em->getRepository('AppBundle:Site')->findByQuery($request);

        return $this->render(
            'admin/site/index.html.twig',
            [
                'sites' => $sites,
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
        $site = new Site();
        $form = $this->createForm(AdminNewSiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new site
            $em = $this->get('doctrine')->getManager();
            $em->persist($site);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'La site <strong>&quot;'.$site
                .'&quot;</strong> a bien été ajoutée.'
            );

            // User is redirected to referrer site
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/site/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish":
     *                                   "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Site                    $site
     * @param                                           $publish
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction(Request $request, Site $site, $publish)
    {
        $site->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($site);
        $em->flush();

        if ($publish) {
            $message = 'La site <strong>&quot;'.$site
                       .'&quot;</strong> a bien été publiée.';
        } else {
            $message = 'La publication de la site <strong>&quot;'.$site
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
     * @param \AppBundle\Entity\Site                    $site
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Site $site)
    {
        $form = $this->createForm(AdminEditSiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited site
            $em = $this->get('doctrine')->getManager();
            $em->persist($site);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'La site <strong>&quot;'.$site
                .'&quot;</strong> a bien été modifiée.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/site/edit.html.twig',
            [
                'form' => $form->createView(),
                'site' => $site,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Site                    $site
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Site $site)
    {
        if ( ! $this->get('security.authorization_checker')->isGranted(
            'ROLE_SUPER_ADMIN'
        )) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$this->getUser().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            // User is redirected to referrer site
            return $this->redirect($request->get('callback'));
        }

        // Deletes specified site
        $em = $this->get('doctrine')->getManager();
        $em->remove($site);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La site <strong>&quot;'.$site
            .'&quot;</strong> a bien été supprimée.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
