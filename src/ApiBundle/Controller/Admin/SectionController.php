<?php

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/admin/sections")
 */
class SectionController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $sections = $em->getRepository('AppBundle:Section')->findByQueryScalar($request);

        return new JsonResponse(
            ['sections' => $sections]
        );
    }
}
