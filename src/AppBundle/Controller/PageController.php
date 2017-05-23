<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use AppBundle\Entity\Stats;
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
        $page = $em->getRepository('AppBundle:Page')->findOneBy(['slug' => $slug]);

        if (!$page) {
            throw $this->createNotFoundException('Cette page n\'existe pas.');
        }

        $this->addView($page);

        return $this->render(
            'page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }

    /**
     * @param Page $page
     */
    public function addView(Page $page)
    {
        $em = $this->get('doctrine')->getManager();

        // Get page stats
        $stats = $page->getStats();

        // When not found creates new stats object
        if (!$stats) {
            $stats = new Stats();
            // Links stats & pages
            // $stats->addPage($page);
            $page->setStats($stats);
        }

        $stats->addView();
        $em->persist($stats);
        $em->persist($page);
        $em->flush();
    }
}
