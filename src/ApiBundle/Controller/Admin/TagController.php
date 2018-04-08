<?php
/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/api/admin/tags")
 */
class TagController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em   = $this->get('doctrine')->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findByQueryScalar(
            $request
        );

        return new JsonResponse(
            ['tags' => $tags]
        );
    }
}
