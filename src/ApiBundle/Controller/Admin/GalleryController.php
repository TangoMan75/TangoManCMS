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
 * Class GalleryController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/api/admin/galleries")
 */
class GalleryController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em        = $this->get('doctrine')->getManager();
        $galleries = $em->getRepository('AppBundle:Gallery')->findByQueryScalar(
            $request,
            ['type' => 'galery']
        );

        return new JsonResponse(
            ['galleries' => $galleries]
        );
    }
}
