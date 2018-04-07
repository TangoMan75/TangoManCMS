<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/page")
 */
class PageController extends Controller
{

    /**
     * @Route("/{slug}", requirements={"slug": "[\w-]+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $page = $em->getRepository('AppBundle:Page')->findOneBy(
            ['slug' => $slug]
        );

        if ( ! $page) {
            throw $this->createNotFoundException('Cette page n\'existe pas.');
        }

        $page->addView();

        return $this->render(
            'page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
