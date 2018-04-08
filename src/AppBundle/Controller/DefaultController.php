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

use AppBundle\Entity\Post;
use AppBundle\Form\NewPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em   = $this->get('doctrine')->getManager();
        $page = $em->getRepository('AppBundle:Page')->findOneBy(
            [
                'slug'      => 'homepage',
                'published' => true,
            ]
        );

        if ($page) {
            $page->addView();

            return $this->render(
                'page/show.html.twig',
                [
                    'page' => $page,
                ]
            );
        }

        // Else just display a bunch of posts
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

        $formPost = null;

        if ($this->get('security.authorization_checker')->isGranted(
            'IS_AUTHENTICATED_REMEMBERED'
        )) {
            $post = new Post();
            $form = $this->createForm(NewPostType::class, $post);
            $form->handleRequest($request);
            $formPost = $form->createView();

            if ($form->isValid()) {
                $post->setUser($this->getUser());
                $em->persist($post);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre article a bien été enregistré.'
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'form'  => $formPost,
                'posts' => $posts,
            ]
        );
    }

    /**
     * @Route("/sitemap.xml", defaults={"_format"="xml"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sitemapAction()
    {
        $em    = $this->get('doctrine')->getManager();
        $pages = $em->getRepository('AppBundle:Page')->findBy(
            ['published' => true]
        );

        return $this->render(
            'sitemap.xml.twig',
            [
                'pages' => $pages,
            ]
        );
    }
}
