<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\PostType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function indexAction(Request $request)
    {
        // $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->find(1);

        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findAll();

        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy([], ['datetime' => 'desc']);

        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['author' => 'Fabrice']);

        // $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->idSuperior(1);

        // dump($listPost);

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($post);

            $manager->flush();

            $this->get('session')->getFlashBag()->add('success','Votre message a bien été enregistré');

            return $this->redirectToRoute('app_homepage');
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'form_post' => $form->createView(),
            'list_post' => $listPost
        ]);
    }

    /**
     * @Route("/view/{id}", name="app_view")
     */
    public function viewAction($id)
    {
        $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['id' => $id]);

        dump($post);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/post", name="app_post")
     */
    public function editAction($id, Request $request)
    {

        $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['id' => $id]);

        $form = $this->createForm(PostType::class,$post);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($post);

            $manager->flush();

            $this->get('session')->getFlashBag()->add('success','Votre message a bien été enregistré');

            return $this->redirectToRoute('app_homepage');
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'form_post' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_delete")
     */
    public function deleteAction(Request $request)
    {
        $post = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->find(1);

        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findAll();

        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy([], ['datetime' => 'desc']);

        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->findBy(['author' => 'Fabrice']);

        $listPost = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post')->idSuperior(1);

        dump($listPost);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
