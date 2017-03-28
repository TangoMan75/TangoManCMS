<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        return $this->render(
            'admin/default/index.html.twig',
            [
                'currentUser'  => $this->getUser(),
                'userCount'    => $this->get('doctrine')->getRepository('AppBundle:User')->count(),
                'pageCount'    => $this->get('doctrine')->getRepository('AppBundle:Page')->count(),
                'postCount'    => $this->get('doctrine')->getRepository('AppBundle:Post')->count(),
                'mediaCount'   => $this->get('doctrine')->getRepository('AppBundle:Media')->count(),
                'commentCount' => $this->get('doctrine')->getRepository('AppBundle:Comment')->count(),
            ]
        );
    }
}
