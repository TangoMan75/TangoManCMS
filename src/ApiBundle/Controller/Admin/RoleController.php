<?php

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/admin/roles")
 */
class RoleController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $roles = $em->getRepository('AppBundle:Role')->findByQueryScalar($request);

        return new JsonResponse(
            ['roles' => $roles]
        );
    }
}
