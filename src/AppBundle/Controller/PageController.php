<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use AppBundle\Entity\Stat;
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

        // Get page stat
        $stat = $page->getStat();

        // When not found creates new stat object
        if (!$stat) {
            $stat = new Stat();
            // Links stat & pages
            // $stat->addPage($page);
            $page->setStat($stat);
        }

        $stat->addView();
        $em->persist($stat);
        $em->persist($page);
        $em->flush();
    }
}
