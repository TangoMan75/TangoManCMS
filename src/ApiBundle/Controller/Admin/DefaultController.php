<?php

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $users = $em->getRepository('AppBundle:User')->export($request->query);

        return new JsonResponse(
            ['users' => $users]
        );
    }
}
