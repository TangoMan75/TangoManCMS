<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * Displays homepage.
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findAllPaged($request->query->getInt('page', 1), 5);
        $formPost = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
            $post = new Post();
            $post->setUser($user);
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            $formPost = $form->createView();

            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Votre message a bien été enregistré.');

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'formPost' => $formPost,
                'posts'    => $posts,
            ]
        );
    }
}
