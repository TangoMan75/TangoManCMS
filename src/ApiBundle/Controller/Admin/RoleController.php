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
 * Class RoleController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/api/admin/roles")
 */
class RoleController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine')->getManager();
        $roles = $em->getRepository('AppBundle:Role')->findByQueryScalar(
            $request
        );

        return new JsonResponse(
            ['roles' => $roles]
        );
    }
}
