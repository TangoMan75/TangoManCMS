<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\NewPostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $page = $em->getRepository('AppBundle:Page')->findOneBy(['slug' => 'homepage']);

        if ($page) {
            $page->addView();

            return $this->render(
                'page/show.html.twig',
                [
                    'page' => $page,
                ]
            );
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $posts = $em->getRepository('AppBundle:Post')->findByQuery($request->query);
        } else {
            $posts = $em->getRepository('AppBundle:Post')->findByQuery($request->query, ['published' => true]);
        }

        $formPost = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $post = new Post();
            $form = $this->createForm(NewPostType::class, $post);
            $form->handleRequest($request);
            $formPost = $form->createView();

            if ($form->isValid()) {
                $post->setUser($this->getUser());
                $em->persist($post);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Votre article a bien été enregistré.');

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
                'form'  => $formPost,
                'posts' => $posts,
            ]
        );
    }
}
