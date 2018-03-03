<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $page = $em->getRepository('AppBundle:Page')->findOneBy(
            ['slug' => $slug]
        );

        if ( ! $page) {
            throw $this->createNotFoundException('Cette page n\'existe pas.');
        }

        $page->addView();

        return $this->render(
            'page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
