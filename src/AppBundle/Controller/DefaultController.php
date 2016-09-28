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
     * @Route("/", name="app_homepage")
     */
    public function indexAction(Request $request)
    {
        // $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->find(1);
        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findAll();
        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['author' => 'Fabrice']);
        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->idSuperior(1);
        // $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['id' => $id]);


        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findByPage($request->query->getInt('page', 1));
        $post = new Post();

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $post->setUser($this->getUser());
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success','Votre message a bien été enregistré');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('default/index.html.twig', [
            'form_post' => $form->createView(),
            'list_post' => $listPost
        ]);
    }

}
