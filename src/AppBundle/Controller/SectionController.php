<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Section;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/section")
 */
class SectionController extends Controller
{

    /**
     * @Route("/{slug}", requirements={"slug": "[\w-]+"})
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->get('doctrine')->getManager();
        $section = $em->getRepository('AppBundle:Section')->findOneBy(
            ['slug' => $slug]
        );

        if ( ! $section) {
            throw $this->createNotFoundException(
                'Cette section n\'existe pas.'
            );
        }

        return $this->render(
            'section/show.html.twig',
            [
                'section' => $section,
            ]
        );
    }
}
