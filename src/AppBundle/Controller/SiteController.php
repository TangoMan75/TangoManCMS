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

use AppBundle\Entity\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/site")
 */
class SiteController extends Controller
{

    /**
     * @Route("/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $site = $em->getRepository('AppBundle:Site')->findOneBy(
            ['slug' => $slug]
        );

        if ( ! $site) {
            throw $this->createNotFoundException('Ce site n\'existe pas.');
        }

        return $this->render(
            'site/show.html.twig',
            [
                'site' => $site,
            ]
        );
    }
}
