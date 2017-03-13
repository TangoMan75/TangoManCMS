<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/page")
 */
class PageController extends Controller
{
    /**
     * @Route("/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $page = $em->getRepository('AppBundle:Page')->findOneBy(['slug' => $slug]);
        $sections = $em->getRepository('AppBundle:Section')->findBy(['page' => $page]);

        return $this->render(
            'page/show.html.twig',
            [
                'page'     => $page,
                'sections' => $sections,
            ]
        );
    }
}
