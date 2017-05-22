<?php

namespace AppBundle\Controller;

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

        $session = $this->get('session');
        // $session->start();
        $sessionId = $session->getId();

        // Finds session in stats
        $stats = $em->getRepository('AppBundle:Stats')->findOneBy(['id' => $sessionId]);

        // Adds one view to page statistics
        if (!$stats) {
            $stats
                ->addView()
                ->setId($sessionId)
                ->setItem($page);

            $em->persist($stats);
            $em->flush();
        }

        return $this->render(
            'page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
