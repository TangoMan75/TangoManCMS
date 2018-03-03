<?php

namespace ApiBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/admin/galleries")
 */
class GalleryController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $galleries = $em->getRepository('AppBundle:Gallery')->findByQueryScalar(
            $request,
            ['type' => 'galery']
        );

        return new JsonResponse(
            ['galleries' => $galleries]
        );
    }
}
